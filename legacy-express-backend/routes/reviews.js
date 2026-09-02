const express = require('express');
const router = express.Router();
const {
  createReview,
  getResourceReviews,
  getMyReviews,
  updateReview,
  deleteReview,
  markHelpful
} = require('../controllers/reviewController');
const { protect } = require('../middleware/auth');
const { reviewValidation, validate } = require('../middleware/validation');

router.post('/', protect, reviewValidation, validate, createReview);
router.get('/resource/:resourceId', protect, getResourceReviews);
router.get('/my-reviews', protect, getMyReviews);
router.put('/:id', protect, reviewValidation, validate, updateReview);
router.delete('/:id', protect, deleteReview);
router.put('/:id/helpful', protect, markHelpful);

module.exports = router;