const express = require('express');
const router = express.Router();
const {
  uploadResource,
  getResources,
  getResource,
  downloadResource,
  deleteResource,
  createCategory,
  getCategories
} = require('../controllers/resourceController');
const { protect, admin } = require('../middleware/auth');
const upload = require('../middleware/upload');

// Category routes
router.post('/categories', protect, admin, createCategory);
router.get('/categories', protect, getCategories);

// Resource routes
router.post('/upload', protect, admin, upload.single('file'), uploadResource);
router.get('/', protect, getResources);
router.get('/:id', protect, getResource);
router.get('/:id/download', protect, downloadResource);
router.delete('/:id', protect, admin, deleteResource);

module.exports = router;