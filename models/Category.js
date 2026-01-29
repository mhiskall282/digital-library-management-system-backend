const mongoose = require('mongoose');

const categorySchema = new mongoose.Schema({
  name: {
    type: String,
    required: true,
    trim: true
  },
  level: {
    type: String,
    required: true,
    enum: ['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD']
  },
  courseCode: {
    type: String,
    required: true,
    trim: true
  },
  courseName: {
    type: String,
    required: true,
    trim: true
  },
  semester: {
    type: String,
    enum: ['FIRST', 'SECOND'],
    required: true
  }
}, {
  timestamps: true
});

module.exports = mongoose.model('Category', categorySchema);