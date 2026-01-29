const multer = require('multer');
const path = require('path');

// Configure storage
const storage = multer.diskStorage({
  destination: function(req, file, cb) {
    // Determine folder based on resource type
    const type = req.body.type;
    let folder = 'uploads/';
    
    if (type === 'SLIDE') {
      folder += 'slides/';
    } else if (type === 'PAST_QUESTION') {
      folder += 'past-questions/';
    }
    
    cb(null, folder);
  },
  filename: function(req, file, cb) {
    // Create unique filename: timestamp-originalname
    const uniqueName = Date.now() + '-' + file.originalname.replace(/\s+/g, '-');
    cb(null, uniqueName);
  }
});

// File filter - only allow PDFs and common document formats
const fileFilter = (req, file, cb) => {
  const allowedTypes = /pdf|doc|docx|ppt|pptx/;
  const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
  const mimetype = allowedTypes.test(file.mimetype);

  if (extname && mimetype) {
    cb(null, true);
  } else {
    cb(new Error('Only PDF, DOC, DOCX, PPT, and PPTX files are allowed'));
  }
};

// Create multer upload instance
const upload = multer({
  storage: storage,
  fileFilter: fileFilter,
  limits: {
    fileSize: 10 * 1024 * 1024 // 10MB limit per file
  }
});

// Export different upload configurations
module.exports = {
  single: upload.single('file'),
  multiple: upload.array('files', 10) // Max 10 files at once
};