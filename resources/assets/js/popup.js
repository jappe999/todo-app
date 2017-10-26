export default {
  name: 'popup',
  props: ['item', 'is_open', 'files'],
  data() {
    return {
      title_edit: false,
      description_edit: false,
      assignee_edit: false,
      files_to_upload: [],
      users: []
    }
  },
  methods: {
    parent_has_class(element, classname) {
      if (element.className.split(' ').indexOf(classname) >= 0)
        return true;

      // Recursive calling
      return element.parentNode.tagName !== 'BODY'  && this.parent_has_class(element.parentNode, classname);
    },
    close_popup() {
      this.update_item();
      this.$emit('close_popup');
    },
    update_item() {
      axios.post('/api/tasks/update', this.item)
      .then(response => {
        if (response.data.status === 'success') {
          console.log(response.data.data);
        }
      })
      .catch(err => {
        console.error(err);
      });
    },
    edit_title(event) {
      // Save parent node to ...
      var parent = event.target.parentNode;

      this.title_edit       = true;
      this.description_edit = false;
      this.assignee_edit = false;

      // ...focus on the input field
      setTimeout(function() {
        parent.childNodes[0].focus();
      }, 100);
    },
    edit_description() {
      this.description_edit = true;
      this.title_edit = false;
      this.assignee_edit = false;
    },
    get_users() {
      axios.get('/api/users/get')
      .then(response => {
        console.log(this.item);
        if (response.data.status === 'success')
          this.users = response.data.data;
      })
    },
    user_is_assigned() {
      if (this.item.assignee)
        return user.id === this.item.assignee.id;
      return false;
    },
    edit_assignee() {
      this.get_users();
      this.assignee_edit = true;
      this.description_edit = false;
      this.title_edit = false;
    },
    select_files(event) {
      var file_input = this.$refs.file_upload;
      file_input.click();
    },
    // Set filenames in fileinput
    set_files() {
      var file_input = this.$refs.file_upload,
          files      = file_input.files;
      this.files_to_upload = [];
      for (var file in files) {
        if (typeof files[file] === 'object')
          this.files_to_upload.push(files[file].name);
      }
    },
    upload_files() {
      var file_input = this.$refs.file_upload,
          files      = file_input.files;

      // Check for files
      if (files.length < 1)
        return;

      for (let file of files) {
        if (typeof file === 'object')
          this.upload(file);
      }
    },
    upload(file) {
      var reader     = new FileReader(),
          self       = this,
          input      = {
            task: this.item,
            file: {}
          };

      reader.onload = function() {
        if (this.result.length > 16777215)
          return alert(file.name + ' cannot be greater than 16MiB');

        // Change file object to new file.
        input.file = {
          name: file.name,
          lastModified: file.lastModified,
          size: file.size,
          type: file.type,
          content: this.result
        };

        // Actual upload of file.
        axios.post('/api/files/add', input)
        .then(response => {
          if (response.data.status === 'success') {
            input.file['id'] = response.data.data;
            self.files.push(input.file);
            self.files_to_upload = [];
          }
        })
        .catch(err => {
          console.error(err);
        });
      };

      // Read each file as a base64 blob.
      reader.readAsDataURL(file);
    },
    remove_file(file_id) {
      // Remove file from list
      for (let index in this.files) {
        let file = this.files[index];

        if (file.id === file_id) {
          this.files.splice(index, 1);
        }
      }
    },
    update_close_all() {
      this.title_edit       = false;
      this.description_edit = false;
      this.assignee_edit    = false;

      this.update_item();
    },
    close_all(event) {
      // Check if clicked element isn't the below and if some are open.
      if (!(this.parent_has_class(event.target, 'popup__title') ||
            this.parent_has_class(event.target, 'popup__description') ||
            this.parent_has_class(event.target, 'popup__assignee')) &&
          (this.title_edit || this.description_edit || this.assignee_edit)) {
        this.update_close_all();
      }
    }
  }
}
