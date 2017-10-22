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
      this.$emit('set_done', this.item);
    },
    edit() {
      this.$emit('edit', this.item);
    }
  }
};
