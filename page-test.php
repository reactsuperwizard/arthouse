<?php 
/**
* Template Name: Test Page Template
*/
?>
<!DOCTYPE html>
<html>
<head>

<?php wp_head(); ?>
<style>
  .rsm-container {
    box-sizing: border-box;
  }

  .row {
    display: flex;
  }

  .w-80 {
    width: 80%
  }

  .w-50 {
    width: 50%
  }

  .w-20 {
    width: 20%
  }

  img[src=""] {
    display: none;
  }

  .rsm-hidden {display: none;}

  .rsm-overlay {
    position: fixed;
    width: 100%;
    height: 100%;
    z-index: 99;
    background: rgb(0,0,0,0.8);
    left: 0;
    top: 0;
  }

  .rsm-preview {
    position: fixed;
    width: 80%;
    height: 80%;
    z-index: 100;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 5px;
    overflow-y: auto;
  }

  .rsm-preview .rsm-close-preview {
    float: right;
    width: 24px;
    height: 24px;
    font-size: 24px;
    padding: 0;
    line-height: 0;
    color: #333;
    border-radius: 2px;
    border: 1px solid #00a0d2;
    background-color: #00a0d2;
  }

  .rsm-preview .rsm-preview-title {
  }

  .rsm-preview-body {
    display: flex;
  }

  .rsm-controller-wrapper {
    padding: 10px;
  }
</style>
</head>
<body>

<div class="rsm-container">
  <div class="row">
    <div class="photo-block w-50">
      <img class="art-img" src="<?php echo get_stylesheet_directory_uri() . '/images/photo.jpg'  ?>">
    </div>
    <div class="editor-block w-50">
      <div class="upload-block">
        <input type="file" onchange="previewFile()"><br>
        <!-- <img id="background-img" src="" height="200" alt="Image preview..."> -->
        <img id="background-img" src="<?php echo get_stylesheet_directory_uri() . '/images/background.jpg'  ?>" height="200">
        <input id="btn-merge" class="" type="button" value="Merge!" />
      </div>
      <div class="rsm-overlay rsm-hidden"></div>
      <div class="rsm-preview rsm-hidden">
        <div class="rsm-preview-title">
          <span>Photo Merge Preview</span>
          <button class="rsm-close-preview">x</button>
        </div>
        <div class="rsm-preview-body">
          <div class="rsm-merged-preview w-80">
            <img id="merged-image" class="" alt="merged image" />
            <canvas id="merged-canvas" class="rsm-hidden" width="480" height="360"></canvas>
          </div>
          <div class="rsm-controller-wrapper w-20">
            <label>Zoom</label><input class="rsm-controller" type="number" name="zoom" value="2.00" step="0.1">
            <label>X</label><input class="rsm-controller" type="number" name="mX" value="200">
            <label>Y</label><input class="rsm-controller" type="number" name="mY" value="200">
            <label>RotationZ</label><input class="rsm-controller" type="number" name="rZ" value="45">
            <label>Skew</label><input class="rsm-controller" type="number" name="skew" value="0.4" step="0.01" min="-0.50" max="0.50">
            <input id="btn-save" type="button" value="Save" />
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<br />

<script>
  $ = jQuery.noConflict();
  var mergedImage = '';

  // file upload preview function
  function previewFile(){
    var preview = document.getElementById('background-img'); //selects the query named img
    var file    = document.querySelector('input[type=file]').files[0]; //sames as here
    var reader  = new FileReader();

    reader.onloadend = function () {
      preview.src = reader.result;
      $('#btn-merge').show();
    }

    if (file) {
      reader.readAsDataURL(file); //reads the data as a URL
    } else {
      preview.src = "";
    }
  }

  // merge 2 photos
  function rsm_merge_photo() {
  	var canvas = document.getElementById('merged-canvas');
  	var context = canvas.getContext('2d');
    var imageBackground = new Image();
    var imagePicture = new Image();

    context.restore();
    context.save();

  	imageBackground.src = $('#background-img').attr('src');
    imageBackground.onload = function() {
      var stageWidth = imageBackground.width;
      var stageHeight = imageBackground.height;

      // var cX = $('input[name="rZ"]').val();
      // var cY = $('input[name="rZ"]').val();

      var rZ = $('input[name="rZ"]').val();
      var mX = $('input[name="mX"]').val();
      var mY = $('input[name="mY"]').val();
      var zoom = $('input[name="zoom"]').val();
      var skew = $('input[name="skew"]').val();

      canvas.width = stageWidth;
      canvas.height = stageHeight;

      context.globalAlpha = 1;
      context.drawImage(imageBackground, 0, 0);


      imagePicture.src = $('.art-img').attr('src');;
      imagePicture.onload = function() {
        var artWidth = imagePicture.width * zoom;
        var artHeight = imagePicture.height * zoom;

        if (skew == 0) {
          context.translate(mX,mY);
          context.rotate(rZ * Math.PI / 180);
          context.drawImage(imagePicture, 0, 0, artWidth, artHeight);
        }

        for (var i = 0; i <= artHeight / 2; ++i) {
          context.setTransform(zoom, skew * i / artHeight, 0, zoom, mX, mY);
          context.rotate(rZ * Math.PI / 180);
          context.drawImage(imagePicture, 0, artHeight / 2 - i, artWidth, 2, 0, artHeight / 2 - i, artWidth, 2);
          context.setTransform(zoom, -skew * i / artHeight, 0, zoom, mX, mY);
          context.rotate(rZ * Math.PI / 180);
          context.drawImage(imagePicture, 0, artHeight / 2 + i, artWidth, 2, 0, artHeight / 2 + i, artWidth, 2);
        }

        mergedImage = canvas.toDataURL('image/jpeg', 0.5);
        $('#merged-image').attr('src', mergedImage);

      }
    };
  }

  function downloadURI(uri) {
    var a = document.createElement('A');
    a.href = uri;
    a.download = uri.substr(uri.lastIndexOf('/') + 1);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  // ajax upload merged image
  function rsm_upload_image() {
    $.ajax({
      type: "POST",
      url: ajax_url,
      data: {
        action: 'rsm_save_image',
        imgBase64: mergedImage
      },
      success: function(result) {
        if (result.status == 'success' ) {
          downloadURI(result.data);
        } else {
          // error
        }
      }
    });
  }

  // open modal
  function rsm_open_preview() {
    $('.rsm-overlay').show();
    $('.rsm-preview').show();
    rsm_merge_photo();
  }

  // close modal
  function rsm_close_preview() {
    $('.rsm-overlay').hide();
    $('.rsm-preview').hide();
  }

  // previewFile();  //calls the function named previewFile()
  $(document).on('click', '#btn-merge', rsm_open_preview);
  $(document).on('click', '.rsm-overlay, .rsm-close-preview', rsm_close_preview);
  $(document).on('change', '.rsm-controller', rsm_merge_photo);
  $(document).on('click', '#btn-save', function() {
    rsm_upload_image();
  });

</script>
<footer>
  <?php wp_footer() ?>  
</footer>
</body>
</html>