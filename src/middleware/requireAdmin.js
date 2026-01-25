export const requireAdmin = (req, res, next) => {
  const isAdmin = req.user?.admin === true;

  if (!isAdmin) {
    return res.status(403).json({ error: "Admin access required" });
  }

  next();
};
