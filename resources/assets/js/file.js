export default {
  name: "navbar",
  props: ['file'],
  data() {
    return {

    }
  },
  methods: {
    delete_file(file) {
      axios.post('/api/files/delete', { 'id': file.id })
      .then(response => {
        if (response.data.status === 'success') {
          this.$emit('remove_file', file.id);
        }
      })
      .catch(err => {
        console.error(err);
      })
    },
  }
};
