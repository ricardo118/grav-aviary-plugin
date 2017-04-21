/**
 * Created by Ricardo on 18/04/2017.
 */

$(document).ready(function () {

    // TODO, be able to use this variable to overwrite the one created in admin/app/pages/media.js
    const previewTemplate = `
    <div class="dz-preview dz-image-preview">
      <div class="dz-details">
        <div class="dz-filename"><span data-dz-name></span></div>
        <div class="dz-size" data-dz-size></div>
        <img data-dz-thumbnail />
      </div>
      <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
      <div class="dz-success-mark"><span>✔</span></div>
      <div class="dz-error-mark"><span>✘</span></div>
      <div class="dz-error-message"><span data-dz-errormessage></span></div>
      <a class="dz-remove" title="Delete" href="javascript:undefined;" data-dz-remove>Delete</a>
      <a class="dz-view" title="View" href="#" target="_blank" data-dz-view>View</a>
      <a class="dz-insert" title="Insert" href="javascript:undefined;" data-dz-insert>Insert}</a>
      <a class="dz-edit" title="Launch Editor" href="javascript:undefined;" data-dz-edit>Launch Editor</a>
    </div>`.trim();

    // access the already defined dropzone element
    var myDropzone = Dropzone.forElement("#grav-dropzone");

    //myDropzone.previewsContainer = previewTemplate;
    //$(myDropzone.previewsContainer).html(previewTemplate);
    //console.log(myDropzone.previewsContainer);


    currentImage = document.createElement('img');
    currentImage.id = 'dz-current-image';
    currentImage.src = window.location.origin;

    a = document.createElement('a');
    a.href =  'javascript:undefined;';
    a.className = 'dz-edit';
    a.id = 'dz-edit';
    a.setAttribute('data-dz-view', '');

    myDropzone.on("success", function(file) {
        file.previewTemplate.append(a);
    });

    // Image Editor configuration
    var csdkImageEditor = new Aviary.Feather({
        apiKey: 'bf06a5ee072248539ec95c826d4366f1',
        onSave: function(imageID, newURL) {
            currentImage.src = newURL;
            csdkImageEditor.close();
            console.log(newURL);
        },
        onError: function(errorObj) {
            console.log(errorObj.code);
            console.log(errorObj.message);
            console.log(errorObj.args);
        }
    });

    $(".dz-edit").click(function() {
        return false;
    });
    // Launch Image Editor
    $('#grav-dropzone').on('click', '.dz-edit', function(event) {
        event.preventDefault();
        currentImage.src = currentImage.src + $(this).attr('href').substring(1);
        console.log(currentImage);

         csdkImageEditor.launch({
             image: currentImage,
         });
    });

});