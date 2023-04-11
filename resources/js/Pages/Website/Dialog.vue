<template>
  <app-layout>
    <template #title>Dialog</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>
    <form method="POST" action="" @submit.prevent="submit" enctype="multipart/form-data" @keydown="$page.errors = {}">
      <tabs>
        <tab name="Dialog" icon="mdi-web" :selected="true">
          <jet-input id="message" type="text" label="Message" :error="$page.errors.message" v-model="form.message" autofocus />
          <jet-input-upload button="Upload" title="Image URL" :src="form.image" @upload="form.imageupload = $event" @delete="form.image = null">
          </jet-input-upload>

          <jet-input id="delay" type="number" label="Delay" :error="$page.errors.delay" v-model="form.delay" />
          <jet-input id="button_allow" type="text" label="Allow Button Text" :error="$page.errors.button_allow" v-model="form.button_allow" />
          <jet-input id="button_block" type="text" label="Block Button Text" :error="$page.errors.button_block" v-model="form.button_block" />
          <div class="form-group row">
            <label for="show_percentage" class="col-md-3 col-form-label">Show Percentage</label>
            <div class="col-md-9">
              <span class="px-4">
                {{ form.show_percentage }}%
              </span>
              <vue-slider class="col-md-6" v-model="form.show_percentage" />
              <small class="form-text text-muted">
                Percent to show dialog instead of directly asking for subscription.
              </small>
            </div>
          </div>
        </tab>

        <tab name="Associated Websites" icon="mdi-web" :counter="associatedCount">
          <div class="form-group row">
            <label for="websites" class="col-md-3 col-form-label">
              Associated Websites
              <a href="#" class="d-block p-3" @click.prevent="toggleWebsites">
                <i class="mdi mdi-checkbox-marked-outline"></i> toggle all
              </a>
            </label>
            <div class="col-md-9">
              <select id="websites" name="websites[]" class="form-control" style="height: 400px;" multiple v-model="associated">
                <option class="p-1" v-for="website in websitesList" :value="website.uuid">
                  {{ website.name }}
                </option>
              </select>
            </div>
          </div>
        </tab>

        <jet-button type="submit" :disabled="form.processing">
          Save changes
        </jet-button>

        <inertia-link class="btn btn-gradient-light" :href="route('websites.index')">
          <i class="mdi mdi-close"></i> Cancel
        </inertia-link>
      </tabs>
    </form>
  </app-layout>
</template>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js"></script>
<script>
  import AppLayout from '../../Layouts/AppLayout';
  import Tabs from '../../Layouts/Tabs';
  import Tab from '../../Layouts/Tab';
  import JetInput from '../../Jetstream/Input'
  import JetButton from './../../Jetstream/Button'
  import JetInputUpload from './../../Jetstream/InputUpload'
  import JetFormLine from './../../Jetstream/FormLine'
  import VueSlider from 'vue-slider-component'
  import 'vue-slider-component/theme/antd.css'

  export default {
    components: {
      AppLayout,
      Tabs,
      Tab,
      JetInput,
      JetButton,
      JetInputUpload,
      JetFormLine,
      VueSlider
    },

    props: {
      id: { type: Number, default: null },
      is_global: { type: Number, default: 0 },
      message: { type: String, default: null },
      image: { type: String, default: null },
      delay: { type: Number, default: 0 },
      button_allow: { type: String, default: null },
      button_block: { type: String, default: null },
      show_percentage: { type: Number, default: 100 },
      websites: { type: Array, default() { return [] } },
      websitesList: { type: Array, default() { return [] } },
    },

    data() {
      return {
        form: this.$inertia.form({
          '_method': 'POST',
          id: this.id || null,
          is_global: this.is_global || 0,
          message: this.message || null,
          image: this.image || null,
          imageupload: null,
          delay: this.delay || 0,
          button_allow: this.button_allow || null,
          button_block: this.button_block || null,
          show_percentage: this.show_percentage || 50,
          websites: this.websites || []
        }, {
          bag: 'dialogs',
          resetOnSuccess: false,
        }),
      }
    },

    methods: {
      submit() {
        if (this.id) {
          this.form._method = 'PUT';
          this.form.post(route('dialogs.update', this.id), { preserveScroll: true });
        } else {
          this.form.post(route('dialogs.store'), { preserveScroll: true });
        }
      },

      toggleWebsites () {
        if (this.websitesList.length == this.form.websites.length) {
          this.form.websites = [];
        } else {
          this.form.websites = this.websitesList;
        }
      },
    },

    computed: {
      associatedCount () {
        return this.form.websites.length;
      },
      associated: {
        set: function(val) {
          var obj = this;
          var websites = [];

          val.forEach(function(v) {
            obj.websitesList.forEach(function(web) {
              if (v == web.uuid) {
                websites.push(web);
              }
            });
          });

          this.form.websites = websites;
        },
        get: function() {
          return this.form.websites.map(e => e.uuid);
        }
      },
    }
  }
</script>

<style>
  .vue-slider-process {
    background: linear-gradient(to right, #da8cff, #9a55ff) !important
  }
  .vue-slider-dot-handle {
    border: 2px solid #da8cff;
  }
  .vue-slider:hover .vue-slider-dot-handle {
    border-color: #9a55ff;
  }
  .vue-slider:hover .vue-slider-dot-handle:hover {
    border-color: #9a55ff;
  }
</style>
