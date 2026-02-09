# Python 3.10 base image
FROM python:3.10-slim

# Install system dependencies for OpenCV, dlib, and compilation
RUN apt-get update && apt-get install -y \
    # Build tools (g++, gcc, make)
    build-essential \
    cmake \
    # OpenCV dependencies
    libgl1 \
    libglib2.0-0 \
    libsm6 \
    libxext6 \
    libxrender-dev \
    libgomp1 \
    # Additional tools
    wget \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Upgrade pip
RUN pip install --upgrade pip

# Set working directory
WORKDIR /app

# Copy requirements first for caching
COPY requirements.txt .

# Install Python dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY . .

# Expose Flask port
EXPOSE 5000

# Set environment variables
ENV FLASK_APP=face_service.py
ENV FLASK_ENV=production

# Run Flask app
CMD ["python", "face_service.py"]
