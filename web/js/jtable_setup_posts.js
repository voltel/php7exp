// postsTableContainer

$(document).ready(function () {
  var cContainerId = 'postsTableContainer';
  var cCommentNumIdPrefix = 'comment_num_for_post_id_';

       $('#' + cContainerId).jtable({
           title: 'Table of posts',
           paging: true, //Enable paging
           pageSize: 5, //Set page size (default: 10)
           sorting: true, //Enable sorting
           defaultSorting: 'email ASC', //Set default sorting
           actions: {
               listAction: '/admin/posts/list',
               deleteAction: '/admin/posts/delete'
               /*
               updateAction: '/admin/users/update',
               createAction: '/admin/users/create'
               */
           },
           fields: {
               id: {
                   key: true,
                   create: false,
                   edit: false,
                   list: true,
                   title: 'Id',
                   width: '6%'
               },
               title: {
                   title: 'Title',
                   width: '15%'
               },
               description: {
                 title: 'Description',
                 width: '45%'
               },
               comments: {
                 title: 'Comments',
                 width: '9%',
                 display: function(data) { // == this_post_data
                   var nCommentsNum = data.record.comments_num;
                   var cIconClass = (nCommentsNum > 0) ? 'icon-comment' : '';

                   var $commentsDisplay = $('<div><span id="'
                      + cCommentNumIdPrefix + data.record.id
                      + '">'
                      + nCommentsNum
                      + '</span>'
                      + '<span class="' + cIconClass + '"></span></div>');

                    if (nCommentsNum > 0) {
                        $commentsDisplay.on('click', function() {
                          showComments(data);
                        });
                    }//endif


                    return $commentsDisplay;
                 } // end of display
               },
               image: {
                   title: 'Image',
                   width: '5%',
                   display: function(data) {
                     if (data.record.image != null) {
                       return '<img src="' + data.record.image + '" width="90" height="90" />';
                     } else {
                       return 'No image';
                     }// end of if
                   }// end of display
               },
               user: {
                 title: 'Author',
                 width: '13%',
                 display: function (data) {
                   return data.record.user.email;
                 }// display
               }, // user
               posted_at: {
                 title: 'Date',
                 width: '10%',
                 display: function(data) {
                   // это только для целей отладки
                   if (! window.lFlag) {
                     window.lFlag = true;
                     console.log("Полученные данные (см. poste_at для таблицы с постами): ");
                     console.dir(data);
                   } // endif

                   var creationDate = Date(data.record.posted_at.date);
                   return creationDate.toString();
                 }// end of display
               }// posted_at
           } // fields
       });

       //Load posts list from server
       $('#' + cContainerId).jtable('load');



       function showComments(post_data) {
         var postId = post_data.record.id;
         var $commentsDisplay = $('#' + cCommentNumIdPrefix + post_data.record.id);

         $('#' + cContainerId)
          .jtable(
            'openChildTable',
            $commentsDisplay.closest('tr'),
             {
               title: 'Post comments',
               actions: {
                 listAction: '/admin/comments/list' + '?postId=' + postId,
                 deleteAction: '/admin/comments/delete' + '?postId=' + postId // id:"2" прийдет в теле запроса POST
               }, // end of actions definition
               fields: {
                 id: { // для deleteAction это значение будет передано как параметр POST запроса id=...
                   key: true,
                   list: false,
                   title: 'Id',
                   width: '5%',

                 },
                 comment: {
                   title: 'Comment',
                   width: '45%'
                 },
                 user: {
                   title: 'User',
                   width: '15%',
                   display: function(data) {
                     return data.record.user.name + ' (' + data.record.user.email + ')';
                   } //end of display
                 },
                 posted_at: {
                   title: 'Date',
                   width: '20%',
                   display: function (data) {
                     var creationDate = Date(data.record.posted_at.date);
                     return creationDate.toString();
                   } // end of display
                 }
               }, //end of fields definitions
               recordDeleted: function() {
                 //var $commentsDisplay = $('#' + cCommentNumIdPrefix + postId);
                 var commentsNum = Number.parseInt($commentsDisplay.html()) - 1;
                 $commentsDisplay.html(commentsNum);
               } //end of recordDeleted
             }, // end of child table definitions

             function (data) {
               data.childTable.jtable('load');
             }
            );
       }//end of fucntion showComments


   });
