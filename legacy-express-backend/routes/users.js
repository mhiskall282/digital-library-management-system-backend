const express = require('express');
const router = express.Router();
const {
  getUsers,
  getUserById,
  updateUser,
  deleteUser
} = require('../controllers/userController');
const { protect, admin } = require('../middleware/auth');

// @desc    Get all users
// @route   GET /api/users
// @access  Private/Admin
router.get('/', protect, admin, getUsers);

// @desc    Get user by ID
// @route   GET /api/users/:id
// @access  Private/Admin
router.get('/:id', protect, admin, getUserById);

// @desc    Update user
// @route   PUT /api/users/:id
// @access  Private/Admin
router.put('/:id', protect, admin, updateUser);

// @desc    Delete user
// @route   DELETE /api/users/:id
// @access  Private/Admin
router.delete('/:id', protect, admin, deleteUser);

module.exports = router;