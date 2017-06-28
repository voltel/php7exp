$(document).ready(function() {

// щелчок по иконке "Комментарии" на странице dashboard
/*
$('.icon-comment-empty').on('click', function() {
  $('#commentModal').modal({});
});
*/

/* Вызов модального окна */
$('#commentModal').on('show.bs.modal', function(e) {

  $("#commentForm")[0].reset();
  console.dir(e);
  var postId = $(e.relatedTarget).attr('data-post_id');
  $('#commentPostId').val(postId);
  getPostComments(postId);

  console.log('Модальное окно вызвано. \nИдентифицирован postId=' + postId + '\n Поля формы очищены.');
  // $('#comments_list').html('');
});


function getPostComments(postId) {
  $('#commentPostId').val(postId);
  $.ajax({
    method: 'GET',
    url: '/comments/post/' + postId,
    cache: false,
  })
  .done(function(response) {
    $('#commentsModal').modal('toggle');
    $('#comments_list').html(response);
  })
  .fail(function() {
    console.error("An error occured while loading comments: " + response.responseText);
    alert("An error occured while loading comments: " + response.responseText);
  });

}//end of function

function getLastComments(postId, num_comments) {
}


$('#commentForm')
  .on('submit', function(e) {
    e.preventDefault();

    var data = $('#commentForm').serialize();

    $.ajax({
      method: "POST",
      url: this.action, // {{ path('post_comment') ==> '/comment'}}
      data: data
    })
    .done(function(response) {
      $('#comments_list').prepend(response);

      var postId = $('#commentPostId').val(); // input:hidden

      // в файле post.html.twig ===> comments_num_{{ post.id }}
      var $commentsNum = $('#num_comments_for_post_' + postId);

      if (!$commentsNum.length) throw "An element for comments not found.";
      var nCommentsNum = parseInt($commentsNum.html());
      $commentsNum.html(nCommentsNum++);

      $('#commentForm')[0].reset();
    })
    .fail(function(response) {
      console.log('An error occurred while submitting the Comment: \n\t' + response.responseText);
      alert('An error occurred while submitting the Comment: ' + response.responseText);
    })
});

}); // end of document ready
