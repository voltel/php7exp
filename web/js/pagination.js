$(document).ready(function() {

function getNextPage(c_button_id, c_url) {
  var pageNum = 2;
  var c_html_next_page_content = '';
  var $e_button;

  // inner function
  function getNextPageContent() {
    $.ajax({
      method: "GET",
      url: c_url + '/' + pageNum // ТУТ БЫЛА ЗАПЯТАЯ!!!, что-то отсутствует - данные!
    })
    .done(function(response) {
      c_html_next_page_content = response.trim();

      if (c_html_next_page_content.length > 0) {
        $e_button.show();
      }//endif
    });
  }//end of inner function getContent

  $e_button = $('#' + c_button_id);

  // следующая страница загружается сразу
  getNextPageContent();

  // но показывается только по щелчку
  $e_button.on('click', function() {
    $e_button.hide();
    $('#posts_list').append(c_html_next_page_content);
    pageNum++;
    getNextPageContent();
  });

}//end of function getNextPage



var c_button_id = 'show_next_page';
getNextPage(c_button_id, '/next_page');

}); // end of document ready
