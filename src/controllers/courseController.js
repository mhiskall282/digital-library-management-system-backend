import { db } from "../config/firebaseAdmin.js";

const LEVELS = new Set([100, 200, 300, 400]);

const normalizeCourseCode = (code) =>
  String(code || "").trim().toUpperCase().replace(/\s+/g, "");

export const listCourses = async (req, res) => {
  try {
    const { level } = req.query;

    let q = db.collection("courses");

    if (level !== undefined) {
      const lvl = Number(level);
      if (!LEVELS.has(lvl)) {
        return res.status(400).json({ error: "level must be one of 100, 200, 300, 400" });
      }
      q = q.where("level", "==", lvl);
    }

    // Optional ordering
    q = q.orderBy("courseCode", "asc");

    const snap = await q.get();
    const courses = snap.docs.map((d) => ({ id: d.id, ...d.data() }));

    return res.json({ ok: true, count: courses.length, courses });
  } catch (err) {
    return res.status(500).json({ error: "Failed to list courses" });
  }
};

export const getCourseById = async (req, res) => {
  try {
    const doc = await db.collection("courses").doc(req.params.id).get();
    if (!doc.exists) return res.status(404).json({ error: "Course not found" });
    return res.json({ ok: true, course: { id: doc.id, ...doc.data() } });
  } catch (err) {
    return res.status(500).json({ error: "Failed to fetch course" });
  }
};

export const createCourse = async (req, res) => {
  try {
    const { level, courseCode, courseName } = req.body;

    const lvl = Number(level);
    const code = normalizeCourseCode(courseCode);
    const name = String(courseName || "").trim();

    if (!LEVELS.has(lvl)) {
      return res.status(400).json({ error: "level must be one of 100, 200, 300, 400" });
    }
    if (!code) return res.status(400).json({ error: "courseCode is required" });
    if (!name) return res.status(400).json({ error: "courseName is required" });

    // prevent duplicates by courseCode (simple approach)
    const existing = await db.collection("courses").where("courseCode", "==", code).limit(1).get();
    if (!existing.empty) {
      return res.status(409).json({ error: "courseCode already exists" });
    }

    const now = new Date().toISOString();

    const payload = {
      level: lvl,
      courseCode: code,
      courseName: name,
      createdAt: now,
      updatedAt: now,
      createdBy: req.user?.uid || null,
    };

    const ref = await db.collection("courses").add(payload);

    return res.status(201).json({ ok: true, id: ref.id, course: payload });
  } catch (err) {
    return res.status(500).json({ error: "Failed to create course" });
  }
};

export const updateCourse = async (req, res) => {
  try {
    const { level, courseCode, courseName } = req.body;

    const ref = db.collection("courses").doc(req.params.id);
    const doc = await ref.get();
    if (!doc.exists) return res.status(404).json({ error: "Course not found" });

    const updates = {};
    if (level !== undefined) {
      const lvl = Number(level);
      if (!LEVELS.has(lvl)) {
        return res.status(400).json({ error: "level must be one of 100, 200, 300, 400" });
      }
      updates.level = lvl;
    }

    if (courseCode !== undefined) {
      const code = normalizeCourseCode(courseCode);
      if (!code) return res.status(400).json({ error: "courseCode cannot be empty" });

      // ensure no other course uses it
      const existing = await db.collection("courses").where("courseCode", "==", code).limit(1).get();
      if (!existing.empty && existing.docs[0].id !== ref.id) {
        return res.status(409).json({ error: "courseCode already exists" });
      }
      updates.courseCode = code;
    }

    if (courseName !== undefined) {
      const name = String(courseName || "").trim();
      if (!name) return res.status(400).json({ error: "courseName cannot be empty" });
      updates.courseName = name;
    }

    updates.updatedAt = new Date().toISOString();

    await ref.update(updates);

    const updated = await ref.get();
    return res.json({ ok: true, course: { id: updated.id, ...updated.data() } });
  } catch (err) {
    return res.status(500).json({ error: "Failed to update course" });
  }
};

export const deleteCourse = async (req, res) => {
  try {
    const ref = db.collection("courses").doc(req.params.id);
    const doc = await ref.get();
    if (!doc.exists) return res.status(404).json({ error: "Course not found" });

    await ref.delete();
    return res.json({ ok: true, message: "Course deleted" });
  } catch (err) {
    return res.status(500).json({ error: "Failed to delete course" });
  }
};
