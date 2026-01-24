import express from "express";
import { requireAuth } from "../middleware/requireAuth.js";
import { requireAdmin } from "../middleware/requireAdmin.js";

const router = express.Router();

// Public
router.get("/public", (req, res) => {
  res.json({ ok: true, message: "Public route works ✅" });
});

// Logged-in (placeholder)
router.get("/me", requireAuth, (req, res) => {
  res.json({ ok: true, message: "Auth route works ✅" });
});

// Admin (placeholder)
router.get("/admin", requireAuth, requireAdmin, (req, res) => {
  res.json({ ok: true, message: "Admin route works ✅" });
});

export default router;
