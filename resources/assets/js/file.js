export default {
  name: "navbar",
  props: ['file'],
  data() {
    return {
        
    }
  },
  methods: {
    delete_file(file) {
      axios.post('/api/files/delete', file)
      .then(response => {
        console.log(response);
      })
      .catch(err => {
        console.error(err);
      })
    },
  }
};
