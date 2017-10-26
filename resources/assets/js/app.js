// Libraries
window.Vue   = require('vue');
window.axios = require('axios');

// Components
import navbar from '../components/navbar.vue';
import todo_item from '../components/todo_item.vue';
import popup from '../components/popup.vue';
import file from '../components/file.vue';

// Component binding
Vue.component('navbar', navbar);
Vue.component('todoitem', todo_item);
Vue.component('popup', popup);
Vue.component('file', file);

var app = new Vue({
    el: '#app',
    data: {
      new_item: {
        title: ''
      },
      user: {},
      items: [],
      selected_item: {},
      files: [],
      popup_open: false
    },
    mounted() {
      this.get_user(),
      this.get_items()
    },
    methods: {
      get_user() {
        axios.get('/api/users/get/me')
        .then(response => {
          if (response.data.status === 'success')
            this.user = response.data.data;
        })
        .catch(err => {
          console.error(err);
        });
      },
      get_items() {
        axios.get('/api/tasks/todo/get')
        .then(response => {
          if (response.data.status === 'success')
            this.items = response.data.data;
        })
        .catch(err => {
          console.error(err);
        });
      },
      add_todo() {
        // If empty
        if (this.new_item.title.trim() === '') return;

        axios.post('/api/tasks/add', this.new_item)
        .then(response => {
          if (response.data.status === 'success')
            this.items.splice(0, 0, JSON.parse(JSON.stringify(response.data.data)));
          this.new_item.title = '';
        })
        .catch(err => {
          console.error(err);
        });
      },
      update_todo(item) {
        axios.post('/api/tasks/update', item)
        .then(response => {
          if (response.data.status === 'success') {
            for (var i in this.items) {
              if (this.items[i].id == item.id) {
                if (item.is_done)
                  this.items.splice(i, 1);
                else
                  this.items[i] = JSON.parse(JSON.stringify(item));
              }
            }
          }
          this.new_item.title = '';
        })
        .catch(err => {
          console.error(err);
        });
      },
      open_popup(item) {
        axios.get('/api/tasks/get/' + item.id)
        .then(response => {
          if (response.data.status === 'success') {
            this.selected_item = response.data.data.task;
            this.files         = response.data.data.files;
            this.popup_open    = true;
          }
        })
        .catch(err => {
          console.error(err);
        });
      },
      close_popup() {
        this.popup_open = false;
      }
    }
});
