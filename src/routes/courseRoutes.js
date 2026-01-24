import express from "express";
import { requireAuth } from "../middleware/requireAuth.js";
import { requireAdmin } from "../middleware/requireAdmin.js";
import {
  listCourses,
  getCourseById,
  createCourse,
  updateCourse,
  deleteCourse,
} from "../controllers/courseController.js";

const router = express.Router();

// Public
router.get("/", listCourses);          // /api/courses?level=200
router.get("/:id", getCourseById);     // /api/courses/:id

// Admin
router.post("/", requireAuth, requireAdmin, createCourse);
router.patch("/:id", requireAuth, requireAdmin, updateCourse);
router.delete("/:id", requireAuth, requireAdmin, deleteCourse);

export default router;
