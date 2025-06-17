<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Signature Pad</title>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad"></script>
    <style>
        canvas {
            border: 2px solid #000;
            width: 100%;
            max-width: 400px;
            height: 150px;
        }
    </style>
</head>
<body>
    <h2>HR Signature</h2>
    <canvas id="signature-pad"></canvas>
    <button onclick="clearSignature()">Clear</button>
    <button onclick="saveSignature()">Save</button>
    <p id="status"></p>

    <script>
        let canvas = document.getElementById("signature-pad");
        let signaturePad = new SignaturePad(canvas);

        function clearSignature() {
            signaturePad.clear();
        }

        function saveSignature() {
            if (signaturePad.isEmpty()) {
                document.getElementById("status").innerText = "Please sign before saving.";
                return;
            }

            let dataURL = signaturePad.toDataURL("image/png");
            fetch("contracts/save_signature.php", {  // ✅ Corrected path
                method: "POST",
                body: JSON.stringify({ signature: dataURL }),
                headers: { "Content-Type": "application/json" }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("status").innerText = data.message;
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById("status").innerText = "Error saving signature.";
            });
        }
    </script>
</body>
</html>
