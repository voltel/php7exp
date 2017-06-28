// usersTableContainer

$(document).ready(function () {

       $('#usersTableContainer').jtable({
           title: 'Table of Users',
           paging: true, //Enable paging
           pageSize: 5, //Set page size (default: 10)
           sorting: true, //Enable sorting
           defaultSorting: 'email ASC', //Set default sorting
           actions: {
               listAction: '/admin/users/list',
               deleteAction: '/admin/users/delete',
               updateAction: '/admin/users/update',
               createAction: '/admin/users/create'
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
               email: {
                   title: 'E-mail',
                   width: '34%'
               },
               name: {
                   title: 'Name',
                   width: '20%',
                   defaultValue: 'User XX'
               },
               password: {
                   title: 'Password',
                   list: false,
                   width: '20%',
                   type: 'password',
                   input: function(data) {
                     //console.log("Argument \"data\" from password field definition in jtable_setup.js: "); console.dir(data);
                     // Объект, есть свойства: form {}, formType: "edit", record {} - здесь все данные формы, value
                     return '<input type="password" name="password" value="" />';
                   }// end of input
               },
               role: {
                   title: 'Role',
                   width: '10%',
                   options: { 'user': 'User Role', 'admin': 'Admin role' }
               }
           } // fields
       });

       //Load users list from server
       $('#usersTableContainer').jtable('load');
   });
