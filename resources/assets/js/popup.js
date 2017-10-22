export default {
  name: 'popup',
  props: ['item', 'is_open'],
  data() {
    return {
      title_edit: false,
      description_edit: false
    }
  },
  methods: {
    parent_has_class(element, classname) {
      if (element.className.split(' ').indexOf(classname) >= 0)
        return true;

      // Recursive calling
      return element.parentNode.className && this.parent_has_class(element.parentNode, classname);
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
      // Save parent node to...
      var parent = event.target.parentNode;

      this.title_edit       = true;
      this.description_edit = false;

      // ...focus on the input
      setTimeout(function() {
        parent.childNodes[0].focus();
      }, 100);
    },
    edit_description() {
      this.description_edit = true;
      this.title_edit = false;
    },
    close_all(event) {
      // Check if clicked element isn't the below and if some are open.
      if (!(this.parent_has_class(event.target, 'popup__title') ||
            this.parent_has_class(event.target, 'popup__description') ||
            this.parent_has_class(event.target, 'popup__assignee')) &&
          (this.title_edit || this.description_edit)) {
        this.title_edit = false;
        this.description_edit = false;
        this.update_item();
      }
    }
  }
}
