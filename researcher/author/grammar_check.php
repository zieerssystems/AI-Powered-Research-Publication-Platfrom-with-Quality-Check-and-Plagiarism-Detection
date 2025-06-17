<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grammar Checker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea { width: 100%; height: 200px; }
        #result { margin-top: 20px; padding: 10px; border: 1px solid #ddd; }
        .disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

    <h2>Upload File or Enter Text for Grammar Check</h2>

    <!-- File Upload Form -->
    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" id="fileInput" accept=".txt, .pdf, .docx"><br><br>
        <button type="button" onclick="uploadFile()">Upload & Extract Text</button>
    </form>

    <hr>

    <!-- Text Area for Manual Input -->
    <textarea id="textInput" placeholder="Enter text here..." onkeyup="checkWordLimit()"></textarea>
    <p><strong>Word Count:</strong> <span id="wordCount">0</span> / 5000</p>

    <button id="checkGrammarBtn" onclick="checkGrammar()">Check Grammar</button>
    <button id="downloadBtn" class="disabled" disabled onclick="downloadDocx()">Download Corrected Text</button>

    <div id="result"></div>

    <script>
        function checkWordLimit() {
            let text = document.getElementById("textInput").value;
            let words = text.trim().split(/\s+/).filter(word => word.length > 0);
            let wordCount = words.length;
            document.getElementById("wordCount").innerText = wordCount;
            
            if (wordCount > 5000) {
                alert("Word limit exceeded! Please reduce your text.");
                document.getElementById("checkGrammarBtn").disabled = true;
            } else {
                document.getElementById("checkGrammarBtn").disabled = false;
            }
        }

        async function checkGrammar() {
            let text = document.getElementById("textInput").value;
            if (text.trim() === "") {
                alert("Please enter some text.");
                return;
            }

            let response = await fetch("openai_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ text: text })
            });

            let result = await response.json();
            document.getElementById("result").innerHTML = "<strong>Corrected Text:</strong><br>" + result.corrected;
            document.getElementById("downloadBtn").classList.remove("disabled");
            document.getElementById("downloadBtn").disabled = false;
        }

        function uploadFile() {
            let fileInput = document.getElementById("fileInput").files[0];
            if (!fileInput) {
                alert("Please select a file.");
                return;
            }

            let formData = new FormData();
            formData.append("file", fileInput);

            fetch("upload_handler.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById("textInput").value = data;
                checkWordLimit();
            })
            .catch(error => console.error("Error:", error));
        }

        function downloadDocx() {
            let correctedText = document.getElementById("result").innerText;
            fetch("download_docx.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ text: correctedText })
            })
            .then(response => response.blob())
            .then(blob => {
                let link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "Corrected_Text.docx";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        }
    </script>

</body>
</html>
