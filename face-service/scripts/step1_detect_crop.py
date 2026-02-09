import cv2
from ultralytics import YOLO

# Load model YOLO face
model = YOLO("models/yolo-face.pt")

# Buka webcam laptop
cap = cv2.VideoCapture(0)

while True:
    ret, frame = cap.read()
    if not ret:
        print("Webcam tidak terbaca")
        break

    # Deteksi wajah
    results = model(frame)

    for r in results:
        for box in r.boxes:
            x1, y1, x2, y2 = map(int, box.xyxy[0])

            # Gambar bounding box
            cv2.rectangle(frame, (x1, y1), (x2, y2),
                          (0, 255, 0), 2)

            # Crop wajah
            face = frame[y1:y2, x1:x2]
            if face.size != 0:
                cv2.imshow("Cropped Face", face)

    cv2.imshow("Face Detection", frame)

    if cv2.waitKey(1) == 27:  # ESC
        break

cap.release()
cv2.destroyAllWindows()
