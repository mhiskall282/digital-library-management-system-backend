const express = require('express');
const router = express.Router();
const {
  getDashboardStats,
  getResourceStats,
  getUserStats
} = require('../controllers/analyticsController');
const { protect, admin } = require('../middleware/auth');

router.get('/dashboard', protect, admin, getDashboardStats);
router.get('/resources/:id', protect, admin, getResourceStats);
router.get('/users/:id', protect, admin, getUserStats);

module.exports = router;