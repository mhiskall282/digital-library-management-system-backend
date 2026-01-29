const Resource = require('../models/Resource');
const Category = require('../models/Category');
const fs = require('fs');
const path = require('path');
const notificationService = require('../utils/notificationService');


// @desc    Upload new resource
// @route   POST /api/resources/upload
// @access  Private/Admin
exports.uploadResource = async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ message: 'Please upload a file' });
    }

    const { title, description, type, category, level, academicYear } = req.body;

    // Verify category exists
    const categoryExists = await Category.findById(category);
    if (!categoryExists) {
      // Delete uploaded file if category doesn't exist
      fs.unlinkSync(req.file.path);
      return res.status(404).json({ message: 'Category not found' });
    }

    const resource = await Resource.create({
      title,
      description,
      type,
      category,
      level,
      fileName: req.file.filename,
      filePath: req.file.path,
      fileSize: req.file.size,
      uploadedBy: req.user._id,
      academicYear
    });

    // Notify users of the level about new resource
    try {
      await notificationService.notifyUsersByLevel(level, {
        type: 'NEW_RESOURCE',
        title: 'New Resource Available',
        message: `New ${type === 'SLIDE' ? 'slide' : 'past question'} uploaded: ${title}`,
        resource: resource._id,
        link: `/resources/${resource._id}`
      });
    } catch (notifError) {
      console.error('Error sending notifications:', notifError);
    }

    const populatedResource = await Resource.findById(resource._id)
      .populate('category')
      .populate('uploadedBy', 'firstName lastName email');

    res.status(201).json(populatedResource);
  } catch (error) {
    // Delete file if resource creation fails
    if (req.file) {
      fs.unlinkSync(req.file.path);
    }
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get all resources (with filters)
// @route   GET /api/resources
// @access  Private
exports.getResources = async (req, res) => {
  try {
    const { type, level, category, search } = req.query;

    let query = {};

    // Filter by type
    if (type) {
      query.type = type;
    }

    // Filter by level
    if (level) {
      query.level = level;
    }

    // Filter by category
    if (category) {
      query.category = category;
    }

    // Search by title
    if (search) {
      query.title = { $regex: search, $options: 'i' };
    }

    const resources = await Resource.find(query)
      .populate('category')
      .populate('uploadedBy', 'firstName lastName')
      .sort('-createdAt');

    res.json({
      count: resources.length,
      resources
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get single resource
// @route   GET /api/resources/:id
// @access  Private
exports.getResource = async (req, res) => {
  try {
    const resource = await Resource.findById(req.params.id)
      .populate('category')
      .populate('uploadedBy', 'firstName lastName email');

    if (!resource) {
      return res.status(404).json({ message: 'Resource not found' });
    }

    res.json(resource);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Download resource
// @route   GET /api/resources/:id/download
// @access  Private
exports.downloadResource = async (req, res) => {
  try {
    const resource = await Resource.findById(req.params.id);

    if (!resource) {
      return res.status(404).json({ message: 'Resource not found' });
    }

    // Check if file exists
    if (!fs.existsSync(resource.filePath)) {
      return res.status(404).json({ message: 'File not found on server' });
    }

    // Increment download count
    resource.downloads += 1;
    await resource.save();

    // Send file
    res.download(resource.filePath, resource.fileName);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Delete resource
// @route   DELETE /api/resources/:id
// @access  Private/Admin
exports.deleteResource = async (req, res) => {
  try {
    const resource = await Resource.findById(req.params.id);

    if (!resource) {
      return res.status(404).json({ message: 'Resource not found' });
    }

    // Delete file from filesystem
    if (fs.existsSync(resource.filePath)) {
      fs.unlinkSync(resource.filePath);
    }

    await resource.deleteOne();

    res.json({ message: 'Resource deleted successfully' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Create category
// @route   POST /api/resources/categories
// @access  Private/Admin
exports.createCategory = async (req, res) => {
  try {
    const { name, level, courseCode, courseName, semester } = req.body;

    const category = await Category.create({
      name,
      level,
      courseCode,
      courseName,
      semester
    });

    res.status(201).json(category);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get all categories
// @route   GET /api/resources/categories
// @access  Private
exports.getCategories = async (req, res) => {
  try {
    const { level } = req.query;
    let query = {};

    if (level) {
      query.level = level;
    }

    const categories = await Category.find(query).sort('level courseCode');

    res.json({
      count: categories.length,
      categories
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

//const notificationService = require('../utils/notificationService');

// Add to the end of resourceController.js

// @desc    Upload multiple resources
// @route   POST /api/resources/upload-multiple
// @access  Private/Admin
exports.uploadMultipleResources = async (req, res) => {
  try {
    if (!req.files || req.files.length === 0) {
      return res.status(400).json({ message: 'Please upload at least one file' });
    }

    const { category, level, type, academicYear } = req.body;

    // Verify category exists
    const categoryExists = await Category.findById(category);
    if (!categoryExists) {
      // Delete uploaded files if category doesn't exist
      req.files.forEach(file => {
        if (fs.existsSync(file.path)) {
          fs.unlinkSync(file.path);
        }
      });
      return res.status(404).json({ message: 'Category not found' });
    }

    // Create resources for each file
    const resources = await Promise.all(
      req.files.map(file => 
        Resource.create({
          title: file.originalname.replace(/\.[^/.]+$/, ''), // Remove extension
          type,
          category,
          level,
          fileName: file.filename,
          filePath: file.path,
          fileSize: file.size,
          uploadedBy: req.user._id,
          academicYear
        })
      )
    );

    // Notify users of the level about new resources
    try {
      await notificationService.notifyUsersByLevel(level, {
        type: 'NEW_RESOURCE',
        title: 'New Resources Available',
        message: `${resources.length} new ${type === 'SLIDE' ? 'slide(s)' : 'past question(s)'} uploaded for ${categoryExists.courseName}`,
        link: `/resources?level=${level}&category=${category}`
      });
    } catch (notifError) {
      console.error('Error sending notifications:', notifError);
    }

    const populatedResources = await Resource.find({
      _id: { $in: resources.map(r => r._id) }
    })
      .populate('category')
      .populate('uploadedBy', 'firstName lastName email');

    res.status(201).json({
      count: populatedResources.length,
      resources: populatedResources
    });
  } catch (error) {
    // Delete files if resource creation fails
    if (req.files) {
      req.files.forEach(file => {
        if (fs.existsSync(file.path)) {
          fs.unlinkSync(file.path);
        }
      });
    }
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Advanced search with filters
// @route   GET /api/resources/search/advanced
// @access  Private
exports.advancedSearch = async (req, res) => {
  try {
    const {
      query,
      type,
      level,
      category,
      minRating,
      academicYear,
      sortBy = 'createdAt',
      order = 'desc',
      page = 1,
      limit = 20
    } = req.query;

    let searchQuery = {};

    // Text search
    if (query) {
      searchQuery.$or = [
        { title: { $regex: query, $options: 'i' } },
        { description: { $regex: query, $options: 'i' } },
        { tags: { $in: [new RegExp(query, 'i')] } }
      ];
    }

    // Filters
    if (type) searchQuery.type = type;
    if (level) searchQuery.level = level;
    if (category) searchQuery.category = category;
    if (minRating) searchQuery.averageRating = { $gte: parseFloat(minRating) };
    if (academicYear) searchQuery.academicYear = academicYear;

    // Sorting
    const sortOptions = {};
    sortOptions[sortBy] = order === 'desc' ? -1 : 1;

    // Pagination
    const skip = (parseInt(page) - 1) * parseInt(limit);

    const resources = await Resource.find(searchQuery)
      .populate('category')
      .populate('uploadedBy', 'firstName lastName')
      .sort(sortOptions)
      .skip(skip)
      .limit(parseInt(limit));

    const total = await Resource.countDocuments(searchQuery);

    res.json({
      page: parseInt(page),
      limit: parseInt(limit),
      total,
      pages: Math.ceil(total / parseInt(limit)),
      resources
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};
