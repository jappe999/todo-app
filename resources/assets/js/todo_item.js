export default {
  name: "todoitem",
  props: [
    'item',
    'is_open'
  ],
  data() {
    return {
      is_new_todo: false
    }
  },
  methods: {
    set_done() {
      this.item.is_done = true;
      this.$emit('update_todo', this.item);
    },
    set_todo() {
      this.item.is_done = false;
      this.$emit('update_todo', this.item);
    },
    edit() {
      this.$emit('edit', this.item);
    }
  }
};
