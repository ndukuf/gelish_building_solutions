<?php    
  // // Set the session to use only cookies for security reasons.
  // ini_set('session.use_only_cookies', 1);
  
  // // Enable strict mode for session handling to prevent attacks and improve security.
  // ini_set('session.use_strict_mode', 1);
  
  // // Set the session cookie parameters for a secure connection
  // session_set_cookie_params(['lifetime' => 1800,'domain' => 'localhost','path' => '/','secure' => true,'httponly' => true]);
  
  // // start a new session
  // session_start();
  
  // // Check if the 'last_id_regeneration' session variable is set
  // if (!isset($_SESSION['last_id_regeneration'])) {
  //     // If not set, regenerate the session ID and set the 'last_id_regeneration' session variable to the current time
  //     session_regenerate_id(true);
  //     $_SESSION['last_id_regeneration'] = time();
  // } else {
  //     // If set, define the session regenerate ID interval as 30 minutes (in seconds)
  //     $session_regenerate_id_interval = 60 * 30;
      
  //     // Check if the difference between the current time and the time stored in the 'last_id_regeneration' session variable is greater than or equal to the session regenerate ID interval
  //     if (time() - $_SESSION['last_id_regeneration'] >= $session_regenerate_id_interval) {
  //         // If so, regenerate the session ID and update the 'last_id_regeneration' session variable to the current time
  //         session_regenerate_id(true);
  //         $_SESSION['last_id_regeneration'] = time();
  //     }
  // }

  // // Generate a random salt for the user ID
  // $randomCharactersSalt = 'GBS';

  // // Generate two random characters and convert them to uppercase hexadecimal
  // $randomCharacters = strtoupper(bin2hex(random_bytes(2)));

  // // Concatenate the random characters and the salt to create the user ID
  // $user_id = $randomCharacters. $randomCharactersSalt. $randomCharacters;

  // // Output the generated user ID
  // echo $user_id;

  // $str = '5555';
  // echo password_hash($str,PASSWORD_DEFAULT);
?>
<!-- <!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Upload Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container mt-5">
      <h2 class="mb-4">Upload Your Photo</h2>
      <form>
        <div class="mb-3">
          <label for="formFile" class="form-label">Choose photo</label>
          <input class="form-control" type="file" id="formFile">
        </div>
        <div class="mb-3">
          <img id="preview" src="" alt="Image Preview" class="img-fluid" style="display:none; max-height: 300px;">
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
      </form>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
      document.getElementById('formFile').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
          const preview = document.getElementById('preview');
          preview.src = URL.createObjectURL(file);
          preview.style.display = 'block';
        }
      });
    </script>
  </body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Photo Upload Form with Drag and Drop</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .drag-drop-area {
      border: 2px dashed #007bff;
      padding: 30px;
      text-align: center;
      cursor: pointer;
    }
    .drag-drop-area.drag-over {
      background-color: #f8f9fa;
    }
    #preview {
      max-height: 300px;
      display: none;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h2 class="mb-4">Upload Your Photo</h2>
    <form id="uploadForm">
      <div class="mb-3 drag-drop-area" id="dragDropArea">
        <p>Drag and drop your photo here or click to select</p>
        <input type="file" id="fileInput" class="form-control" style="display: none;">
      </div>
      <div class="mb-3">
        <img id="preview" src="" alt="Image Preview" class="img-fluid">
      </div>
      <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </div>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const dragDropArea = document.getElementById('dragDropArea');
      const fileInput = document.getElementById('fileInput');
      const preview = document.getElementById('preview');

      // Prevent default drag behaviors
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dragDropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
      });

      // Highlight drag area when item is dragged over it
      ['dragenter', 'dragover'].forEach(eventName => {
        dragDropArea.addEventListener(eventName, () => dragDropArea.classList.add('drag-over'), false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dragDropArea.addEventListener(eventName, () => dragDropArea.classList.remove('drag-over'), false);
      });

      // Handle dropped files
      dragDropArea.addEventListener('drop', handleDrop, false);
      dragDropArea.addEventListener('click', () => fileInput.click());
      fileInput.addEventListener('change', handleFiles);

      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files: files } });
      }

      function handleFiles(e) {
        const files = e.target.files;
        if (files.length) {
          const file = files[0];
          preview.src = URL.createObjectURL(file);
          preview.style.display = 'block';
        }
      }
    });
  </script>
</body>
</html>

<?php
 echo $hashPass = password_hash('5555', PASSWORD_DEFAULT);
?>
<?php $dbConnection->close(); // Close the database connection ?>