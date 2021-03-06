<!doctype html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Image rating</title>
    <meta name="description" content="Predict image rating by Nima model">
    <meta name="author" content="SitePoint">

    <meta property="og:title" content="Predict image rating by Nima model">
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:description" content="Predict image rating by Nima model">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--  <meta property="og:image" content="image.png">-->

    <!--  <link rel="icon" href="/favicon.ico">-->
    <!--  <link rel="icon" href="/favicon.svg" type="image/svg+xml">-->
    <!--  <link rel="apple-touch-icon" href="/apple-touch-icon.png">-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <style>

        html, body, * {
            box-sizing: border-box;
            font-size: 16px;
        }

        html, body {
            height: 100%;
            text-align: center;
        }

        body {
            padding: 2rem;
            background: #f8f8f8;
        }

        h2 {
            font-family: "Roboto", sans-serif;
            font-size: 26px;
            line-height: 1;
            color: #454cad;
            margin-bottom: 0;
        }

        p {
            font-family: "Roboto", sans-serif;
            font-size: 18px;
            color: #5f6982;
        }

        .uploader {
            display: block;
            clear: both;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
        }

        .uploader label {
            float: left;
            clear: both;
            width: 100%;
            padding: 2rem 1.5rem;
            text-align: center;
            background: #fff;
            border-radius: 7px;
            border: 3px solid #eee;
            transition: all 0.2s ease;
            user-select: none;
        }

        .uploader label:hover {
            border-color: #454cad;
        }

        .uploader label.hover {
            border: 3px solid #454cad;
            box-shadow: inset 0 0 0 6px #eee;
        }

        .uploader label.hover #start i.fa {
            transform: scale(0.8);
            opacity: 0.3;
        }

        .uploader #start {
            float: left;
            clear: both;
            width: 100%;
        }

        .uploader #start.hidden {
            display: none;
        }

        .uploader #start i.fa {
            font-size: 50px;
            margin-bottom: 1rem;
            transition: all 0.2s ease-in-out;
        }

        .uploader #response {
            float: left;
            clear: both;
            width: 100%;
        }

        .uploader #response.hidden {
            display: none;
        }

        .uploader #response #messages {
            margin-bottom: 0.5rem;
        }

        .preview, .uploader #file-image {
            display: inline;
            margin: 0 auto 0.5rem auto;
            width: auto;
            height: auto;
            max-width: 180px;
        }

        .uploader #file-image.hidden {
            display: none;
        }

        .uploader #notimage {
            display: block;
            float: left;
            clear: both;
            width: 100%;
        }

        .uploader #notimage.hidden {
            display: none;
        }

        .uploader progress, .uploader .progress {
            display: inline;
            clear: both;
            margin: 0 auto;
            width: 100%;
            max-width: 180px;
            height: 8px;
            border: 0;
            border-radius: 4px;
            background-color: #eee;
            overflow: hidden;
        }

        .uploader .progress[value]::-webkit-progress-bar {
            border-radius: 4px;
            background-color: #eee;
        }

        .uploader .progress[value]::-webkit-progress-value {
            background: linear-gradient(to right, #393f90 0%, #454cad 50%);
            border-radius: 4px;
        }

        .uploader .progress[value]::-moz-progress-bar {
            background: linear-gradient(to right, #393f90 0%, #454cad 50%);
            border-radius: 4px;
        }

        .uploader input[type="file"] {
            display: none;
        }

        .uploader div {
            margin: 0 0 0.5rem 0;
            color: #5f6982;
        }

        .uploader .btn {
            display: inline-block;
            margin: 0.5rem 0.5rem 1rem 0.5rem;
            clear: both;
            font-family: inherit;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            text-transform: initial;
            border: none;
            border-radius: 0.2rem;
            outline: none;
            padding: 0 1rem;
            height: 36px;
            line-height: 36px;
            color: #fff;
            transition: all 0.2s ease-in-out;
            box-sizing: border-box;
            background: #454cad;
            border-color: #454cad;
            cursor: pointer;
        }
    </style>
</head>

<body>
<h2>Image Preview</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Upload  -->
<form id="file-upload-form" class="uploader" enctype="multipart/form-data">
    <input id="file-upload" type="file" name="mfiles" accept="image/jpeg" multiple/>

    <label for="file-upload" id="file-drag">
        <div id="start">
            <i class="fa fa-download" aria-hidden="true"></i>
            <div>Select image</div>
            <div id="notimage" class="hidden">Please select jpeg only</div>
            <span id="file-upload-btn" class="btn btn-primary">Select a file</span>
        </div>
        <img id="file-image" src="#" alt="Preview" class="hidden">
        <div id="response" class="hidden">
            <div id="messages"></div>
        </div>
    </label>
</form>
<script>
    // File Upload
    //
    var files = [];

    function ekUpload() {
        function Init() {

            console.log("Upload Initialised");

            var fileSelect = document.getElementById('file-upload'),
                fileDrag = document.getElementById('file-drag'),
                submitButton = document.getElementById('submit-button');

            fileSelect.addEventListener('change', fileSelectHandler, false);

            // Is XHR2 available?
            var xhr = new XMLHttpRequest();
            if (xhr.upload) {
                // File Drop
                fileDrag.addEventListener('dragover', fileDragHover, false);
                fileDrag.addEventListener('dragleave', fileDragHover, false);
                fileDrag.addEventListener('drop', fileSelectHandler, false);
            }
        }

        function fileDragHover(e) {
            var fileDrag = document.getElementById('file-drag');

            e.stopPropagation();
            e.preventDefault();

            fileDrag.className = (e.type === 'dragover' ? 'hover' : 'modal-body file-upload');
        }

        function fileSelectHandler(e) {
            // Fetch FileList object
            var files = e.target.files || e.dataTransfer.files;

            // Cancel event and hover styling
            fileDragHover(e);

            // Process all File objects
            for (var i = 0, f; f = files[i]; i++) {
                parseFile(f, i);
            }
            addButton();
        }

        // Output
        function output(msg) {
            // Response
            var m = document.getElementById('messages');
            m.innerHTML = msg;
        }

        function addButton() {
            var div = document.createElement("div");
            if (!document.getElementById('files-rate')) {
                div.innerHTML = "<span id='files-rate' class='btn btn-primary'>Get rate</span>";
                document.getElementById('file-upload-form').insertBefore(div, null);
                document.getElementById('files-rate').addEventListener('click', uploadFile, false);
            }
        }

        function parseFile(file, i) {

            console.log(file.name);

            var fileType = file.type;
            console.log(fileType);
            var imageName = file.name;

            var isGood = (/\.(?=jpg|jpeg)/gi).test(imageName);
            if (isGood) {
                document.getElementById('start').classList.add("hidden");
                document.getElementById('response').classList.remove("hidden");
                document.getElementById('notimage').classList.add("hidden");
                // Thumbnail Preview
                var div = document.createElement("div");
                div.innerHTML = "<img class='preview thumbnail-" + i + "' src='" + URL.createObjectURL(file) + "'" +
                    " title='" + file.name + "'/><div id='response-" + i +
                    "'><div id='messages-" + i + "'><strong>" + file.name + "</strong></div>";

                document.getElementById('file-drag').insertBefore(div, null);
                files.push(file)
            } else {
                document.getElementById('file-image').classList.add("hidden");
                document.getElementById('notimage').classList.remove("hidden");
                document.getElementById('start').classList.remove("hidden");
                document.getElementById('response').classList.add("hidden");
                document.getElementById("file-upload-form").reset();
            }
        }

        function uploadFile() {
            files.forEach((file) => {
                var fileSizeLimit = 24; // In MB
                if (file.size <= fileSizeLimit * 1024 * 1024) {
                    var formData = new FormData();
                    formData.append('file', file, file.name);
                    formData.append('_token', '{{csrf_token()}}');
                    const upload = (file) => {
                        fetch(document.getElementById('file-upload-form').action, { // Your POST endpoint
                            method: 'POST',
                            body: formData
                        }).then(
                            response => response.json()
                        ).then(
                            success => console.log(success)
                        ).catch(
                            error => console.log(error)
                        );
                    };
                    upload(file);
                } else {
                    output('Please upload a smaller file (< ' + fileSizeLimit + ' MB).');
                }
            })
        }

        // Check for the various File API support.
        if (window.File && window.FileList && window.FileReader) {
            Init();
        } else {
            document.getElementById('file-drag').style.display = 'none';
        }
    }

    ekUpload();
</script>
</body>
</html>
