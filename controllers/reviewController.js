const Review = require('../models/Review');
const Resource = require('../models/Resource');

// @desc    Create a review
// @route   POST /api/reviews
// @access  Private
exports.createReview = async (req, res) => {
  try {
    const { resource, rating, comment } = req.body;

    // Check if resource exists
    const resourceExists = await Resource.findById(resource);
    if (!resourceExists) {
      return res.status(404).json({ message: 'Resource not found' });
    }

    // Check if user already reviewed this resource
    const existingReview = await Review.findOne({
      resource,
      user: req.user._id
    });

    if (existingReview) {
      return res.status(400).json({ 
        message: 'You have already reviewed this resource' 
      });
    }

    // Create review
    const review = await Review.create({
      resource,
      user: req.user._id,
      rating,
      comment
    });

    // Update resource average rating
    await updateResourceRating(resource);

    const populatedReview = await Review.findById(review._id)
      .populate('user', 'firstName lastName studentId')
      .populate('resource', 'title');

    res.status(201).json(populatedReview);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get reviews for a resource
// @route   GET /api/reviews/resource/:resourceId
// @access  Private
exports.getResourceReviews = async (req, res) => {
  try {
    const reviews = await Review.find({ resource: req.params.resourceId })
      .populate('user', 'firstName lastName studentId')
      .sort('-createdAt');

    res.json({
      count: reviews.length,
      reviews
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get user's reviews
// @route   GET /api/reviews/my-reviews
// @access  Private
exports.getMyReviews = async (req, res) => {
  try {
    const reviews = await Review.find({ user: req.user._id })
      .populate('resource', 'title type level')
      .sort('-createdAt');

    res.json({
      count: reviews.length,
      reviews
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Update a review
// @route   PUT /api/reviews/:id
// @access  Private
exports.updateReview = async (req, res) => {
  try {
    const review = await Review.findById(req.params.id);

    if (!review) {
      return res.status(404).json({ message: 'Review not found' });
    }

    // Check if user owns the review
    if (review.user.toString() !== req.user._id.toString()) {
      return res.status(403).json({ 
        message: 'You can only update your own reviews' 
      });
    }

    review.rating = req.body.rating || review.rating;
    review.comment = req.body.comment || review.comment;

    await review.save();

    // Update resource average rating
    await updateResourceRating(review.resource);

    const updatedReview = await Review.findById(review._id)
      .populate('user', 'firstName lastName studentId')
      .populate('resource', 'title');

    res.json(updatedReview);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Delete a review
// @route   DELETE /api/reviews/:id
// @access  Private
exports.deleteReview = async (req, res) => {
  try {
    const review = await Review.findById(req.params.id);

    if (!review) {
      return res.status(404).json({ message: 'Review not found' });
    }

    // Check if user owns the review or is admin
    if (review.user.toString() !== req.user._id.toString() && req.user.role !== 'admin') {
      return res.status(403).json({ 
        message: 'You can only delete your own reviews' 
      });
    }

    const resourceId = review.resource;
    await review.deleteOne();

    // Update resource average rating
    await updateResourceRating(resourceId);

    res.json({ message: 'Review deleted successfully' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Mark review as helpful
// @route   PUT /api/reviews/:id/helpful
// @access  Private
exports.markHelpful = async (req, res) => {
  try {
    const review = await Review.findById(req.params.id);

    if (!review) {
      return res.status(404).json({ message: 'Review not found' });
    }

    review.helpful += 1;
    await review.save();

    res.json(review);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// Helper function to update resource rating
async function updateResourceRating(resourceId) {
  const reviews = await Review.find({ resource: resourceId });
  
  if (reviews.length === 0) {
    await Resource.findByIdAndUpdate(resourceId, {
      averageRating: 0,
      totalReviews: 0
    });
    return;
  }

  const totalRating = reviews.reduce((sum, review) => sum + review.rating, 0);
  const averageRating = totalRating / reviews.length;

  await Resource.findByIdAndUpdate(resourceId, {
    averageRating: averageRating.toFixed(1),
    totalReviews: reviews.length
  });
}