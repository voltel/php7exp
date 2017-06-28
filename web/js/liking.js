/* Like/Unlike the user post */
$(document).ready(function() {

var c_like_button_prefix = 'thumb_up_for_post_';

// Делегированное событие
$('#posts_list')
  .on('click dblclick', '[id^=' + c_like_button_prefix + ']', function(e) {
    var attr_id = e.target.id;
    var postId = attr_id.slice(attr_id.lastIndexOf('_') + 1);
    //console.log('Был определён postId: ' + postId);
    like(postId);
  });


function like(postId) {

  var $e_thumb_up = $('#' + c_like_button_prefix + postId);

  var likeStatus = $e_thumb_up.data('like_status');
  //console.log('Current like status: ' + likeStatus);

  // если пользователь быстро щёлкает - сообщение
  if ($e_thumb_up.data('request_in_progress') == true) {
    //alert('Не торопитесь щёлкать!');
    return false;
  };
  // метка о том, что у данного элемента начинается активный запрос
  $e_thumb_up.data('request_in_progress', true);

  if (likeStatus == 'liked') {
    unlikeRequest($e_thumb_up, postId);
  } else {
    likeRequest($e_thumb_up, postId);
  }//endif
}//end of function



function likeRequest($e_thumb_up, postId) {

  $e_thumb_up.data('request_in_progress', true);

  $.ajax({
    type: "POST",
    url: '/like/post/' + postId,
    data: 'json'
  })
  .done(function(response) {
    $('#num_likes_for_post_' + postId).html(response.num_likes);
    toggleThumbAppearance($e_thumb_up, 'liked');
  })
  .always(function() {
    $e_thumb_up.data('request_in_progress', false);
  });
}//end of function


function unlikeRequest($e_thumb_up, postId) {
  //

  $.ajax({
    type: 'DELETE',
    url: '/like/post/' + postId,
    data: 'json',
    statusCode: {
      500: function() {
            alert('Page ' + '/like/post/' + postId + 'not found');
      } // end of 500
    }//end of statusCode
  })
  .done(function(response) {
    $('#num_likes_for_post_' + postId).html(response.num_likes);
    toggleThumbAppearance($e_thumb_up, 'not_liked');
  })
  .always(function() {
    $e_thumb_up.data('request_in_progress', false);
  });

}//end of function


  function toggleThumbAppearance($e_thumb_up, c_like_status) {
    $e_thumb_up.data('like_status', c_like_status);
    $e_thumb_up
      .toggleClass('icon-thumbs-up icon-thumbs-up-alt')
      .toggleClass('text-muted text-danger');
  } //end of function


}); // end of document ready
