const express = require('express');
const router = express.Router();
const {
  createBookmark,
  getMyBookmarks,
  updateBookmark,
  deleteBookmark,
  checkBookmark
} = require('../controllers/bookmarkController');
const { protect } = require('../middleware/auth');

router.post('/', protect, createBookmark);
router.get('/', protect, getMyBookmarks);
router.get('/check/:resourceId', protect, checkBookmark);
router.put('/:id', protect, updateBookmark);
router.delete('/:id', protect, deleteBookmark);

module.exports = router;