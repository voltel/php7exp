$(document).ready(function() {

  $('#postForm').on('submit', function(e) {
    try {
      //console.info("Submit event");
      e.preventDefault();

      var data = $("#postForm").serialize();
      $.ajax({
          method: "PUT",
          url: this.action,
          data: data
      })
      .done(function(response) {
        $('#posts_list').prepend(response);
        $('#postModal').modal('toggle');
        resetUserPostForm();
      })
      .fail(function(response) {
        console.log("An error during AJAX request. " + response.responseText)
        alert(response.responseText);
      });

    } catch(o_error) {
      console.error('Error: see console for details: ' + o_error.message);
      alert("There was as error in the program. See console for details. ");
    }
    return false;
  });// submit

  function resetUserPostForm() {
    $("#postForm")[0].reset();
    $('#imagePreview').html('');
    $('#progress .bar').css('width', '0%');
  }//end of function

  /* При вызове модального окна - очистить форму */
  //$('#postModal').on('show.bs.modal', resetUserPostForm);

  $('#fileupload').fileupload({
    //type: 'POST', // этого не было
    dataType: 'json',
    url: '/post/upload',
    replaceFileInput: false,
    fileInput: $('input:file'),
    progressall: function (e, data) {
      var progress = parseInt(data.loaded / data.total * 100);
      $('#progress .bar').css('width', progress + '%');
    },
    done: function(e, data) {
      // см. $app['image_path'] и $app['upload_dir'] в файле app\config\common.php
      // также смотри UserPost::uploadImageAction() - добавляется /temp/
      var imgPreview = '<img src="/images/temp/' + data.result.image + '" />';
      $('#imagePreview').html(imgPreview);
      $('#imageName').val(data.result.image);
    },
    fail: function(o_1, o_2) {
      console.error("AJAX request failed. ");
      console.dir(o_2.messages)
      console.open();
      // $('body').prepend(o_2["_response"]['jqXHR']['responseText']); //
      /*
      console.log("arg1: ");
      console.dir(o_1);
      console.log("o_2: ");
      console.dir(o_2);
      */

      alert('Image upload failed, please try again.');
    }
  }); //end of fileupload



});
