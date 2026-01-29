const Bookmark = require('../models/Bookmark');
const Resource = require('../models/Resource');

// @desc    Create/Add bookmark
// @route   POST /api/bookmarks
// @access  Private
exports.createBookmark = async (req, res) => {
  try {
    const { resource, notes } = req.body;

    // Check if resource exists
    const resourceExists = await Resource.findById(resource);
    if (!resourceExists) {
      return res.status(404).json({ message: 'Resource not found' });
    }

    // Check if already bookmarked
    const existingBookmark = await Bookmark.findOne({
      user: req.user._id,
      resource
    });

    if (existingBookmark) {
      return res.status(400).json({ 
        message: 'Resource already bookmarked' 
      });
    }

    // Create bookmark
    const bookmark = await Bookmark.create({
      user: req.user._id,
      resource,
      notes
    });

    const populatedBookmark = await Bookmark.findById(bookmark._id)
      .populate('resource', 'title type level fileName');

    res.status(201).json(populatedBookmark);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get user's bookmarks
// @route   GET /api/bookmarks
// @access  Private
exports.getMyBookmarks = async (req, res) => {
  try {
    const { type, level } = req.query;
    
    let query = { user: req.user._id };

    const bookmarks = await Bookmark.find(query)
      .populate({
        path: 'resource',
        match: {
          ...(type && { type }),
          ...(level && { level })
        },
        populate: {
          path: 'category',
          select: 'name courseCode courseName'
        }
      })
      .sort('-createdAt');

    // Filter out bookmarks where resource was deleted
    const validBookmarks = bookmarks.filter(b => b.resource);

    res.json({
      count: validBookmarks.length,
      bookmarks: validBookmarks
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Update bookmark notes
// @route   PUT /api/bookmarks/:id
// @access  Private
exports.updateBookmark = async (req, res) => {
  try {
    const bookmark = await Bookmark.findById(req.params.id);

    if (!bookmark) {
      return res.status(404).json({ message: 'Bookmark not found' });
    }

    // Check ownership
    if (bookmark.user.toString() !== req.user._id.toString()) {
      return res.status(403).json({ 
        message: 'You can only update your own bookmarks' 
      });
    }

    bookmark.notes = req.body.notes || bookmark.notes;
    await bookmark.save();

    const updatedBookmark = await Bookmark.findById(bookmark._id)
      .populate('resource', 'title type level fileName');

    res.json(updatedBookmark);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Delete bookmark
// @route   DELETE /api/bookmarks/:id
// @access  Private
exports.deleteBookmark = async (req, res) => {
  try {
    const bookmark = await Bookmark.findById(req.params.id);

    if (!bookmark) {
      return res.status(404).json({ message: 'Bookmark not found' });
    }

    // Check ownership
    if (bookmark.user.toString() !== req.user._id.toString()) {
      return res.status(403).json({ 
        message: 'You can only delete your own bookmarks' 
      });
    }

    await bookmark.deleteOne();

    res.json({ message: 'Bookmark removed successfully' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Check if resource is bookmarked
// @route   GET /api/bookmarks/check/:resourceId
// @access  Private
exports.checkBookmark = async (req, res) => {
  try {
    const bookmark = await Bookmark.findOne({
      user: req.user._id,
      resource: req.params.resourceId
    });

    res.json({
      isBookmarked: !!bookmark,
      bookmark: bookmark || null
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};