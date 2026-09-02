const mongoose = require('mongoose');

const reviewSchema = new mongoose.Schema({
  resource: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'Resource',
    required: true
  },
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  rating: {
    type: Number,
    required: true,
    min: 1,
    max: 5
  },
  comment: {
    type: String,
    trim: true,
    maxlength: 500
  },
  helpful: {
    type: Number,
    default: 0
  }
}, {
  timestamps: true
});

// Prevent duplicate reviews from same user
reviewSchema.index({ resource: 1, user: 1 }, { unique: true });

module.exports = mongoose.model('Review', reviewSchema);