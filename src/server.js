import express from "express";
import cors from "cors";
import dotenv from "dotenv";


import testRoutes from "./routes/testRoutes.js"; // adjust if needed
//import "./config/firebaseAdmin.js"; // ensure Firebase admin is initialized
//import "./config/firebase.js"; // ensure Firebase client is initialized
//import "./config/multerConfig.js"; // ensure Multer config is loaded
import courseRoutes from "./routes/courseRoutes.js";
//import userRoutes from "./routes/userRoutes.js";        


dotenv.config();

const app = express(); // ✅ app must be initialized BEFORE using it


// Middlewares
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));


// Routes
app.use("/api/test", testRoutes);
app.use("/api/courses", courseRoutes);
//app.use("/api/users", userRoutes);


// Health check (optional)
app.get("/", (req, res) => {
  res.json({ ok: true, message: "Digital Library API running" });
});

// Start server
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => console.log(`✅ Server running on port ${PORT}`));
