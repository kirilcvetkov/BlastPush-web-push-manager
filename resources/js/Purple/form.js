
class Errors {
  constructor() {
    this.errors = {};
  }

  has(field) {
    return this.errors.hasOwnProperty(field);
  }

  any() {
    return Object.keys(this.errors).length > 0;
  }

  get(field) {
    if (this.errors[field]) {
      return this.errors[field][0];
    }
  }

  clear(field) {
    if (field) {
      delete this.errors[field];
      return;
    }

    this.errors = {};
  }

  record(errors) {
    this.errors = errors;
  }
}

class Form {
  constructor(data) {
    this.loading = false;
    this.originalData = data;

    for (let field in data) {
      this[field] = data[field];
    }

    this.errors = new Errors();
  }

  data() {
    let data = Object.assign({}, this);
    delete data.originalData;
    delete data.errors;
    return data;
  }

  reset() {
    for (let field in this.originalData) {
      this[field] = '';
    }
  }

  submit() {
    this.$inertia.post('/users', this.form)
    this.loading = true;

    return new Promise((resolve, reject) => {
      axios.[requestType](url, this.data())
        .then(response => {
          this.onSuccess(response.data);
          this.loading = false;

          resolve(response.data);
        })
        .catch(errors => {
          this.onFail(errors.response.data.errors);
          this.loading = false;

          reject(errors.response.data.errors);
        });
      });
  }

  shouldDisable() {
    return this.errors.any() || this.loading;
  }

  onSuccess(data) {
    alert(data);

    this.errors.clear();
    this.reset();

    return true;
  }

  onFail(errors) {
    this.errors.record(errors);
  }
}
