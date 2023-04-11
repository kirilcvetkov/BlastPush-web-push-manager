<template>
  <app-layout>
    <template #title>Website</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>

    <form method="POST" action="" @submit.prevent="submit" enctype="multipart/form-data"
      @keydown="$page.errors = {}">

      <tabs>
        <tab name="Website" icon="mdi-web" :selected="true">
          <div class="form-group row">
            <label for="website_uuid" class="col-md-3 col-form-label">Key</label>
            <div class="col-md-9">
              <label class="col-form-label" id="website_uuid">{{ uuid }}</label>
            </div>
          </div>

          <jet-input id="name" type="text" label="Name" :error="$page.errors.name" v-model="form.name" autofocus />

          <jet-input id="domain" type="text" label="Domain" :error="$page.errors.domain" v-model="form.domain" />

          <jet-form-line id="dedupe_subscribers" label="Dedupe Subscribers">
            <label class="form-check-label">
              <input id="dedupe_subscribers" type="checkbox" class="form-control" v-model="form.dedupe_subscribers"
                :class="{'is-invalid': $page.errors.dedupe_subscribers}" />
              <i class="input-helper"></i>
              <p class="text-muted">
                Subscribers will be deduped for the whole account regardless of which website they arrived from.
              </p>
              <span class="invalid-feedback d-block" role="alert" v-if="$page.errors.dedupe_subscribers">
                <strong>{{ $page.errors.dedupe_subscribers }}</strong>
              </span>
            </label>
          </jet-form-line>

          <jet-input-upload button="Upload" title="Default Icon" :src="form.icon" @upload="form.iconupload = $event" @delete="form.icon = null">
          </jet-input-upload>

          <jet-input-upload button="Upload" title="Default Image" :src="form.image" @upload="form.imageupload = $event" @delete="form.image = null">
          </jet-input-upload>
        </tab>

        <tab name="Associated Campaigns" icon="mdi-group" :counter="associatedCount">
          <div class="form-group row">
            <label for="campaigns" class="col-md-3 col-form-label">
              Associated Campaigns
              <a href="#" class="d-block p-3" @click.stop="toggleCampaigns">
                <i class="mdi mdi-checkbox-marked-outline"></i> toggle all
              </a>
            </label>
            <div class="col-md-9">
              <select id="campaigns" name="campaigns[]" class="form-control" style="height: 400px;" multiple v-model="associated">
                <option class="p-1" v-for="campaign in campaignsList" :value="campaign.id">
                  {{ campaign.name }} - {{ campaign.type.charAt(0).toUpperCase() + campaign.type.slice(1) }}
                </option>
              </select>
            </div>
          </div>
        </tab>

        <tab name="Webhook" icon="mdi-webhook" :counter="typesCount">
          <jet-form-line id="webhook_url" label="Webhook URL">
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <select id="webhook_method" name="webhook_method" class="form-control" v-model="form.webhook_method">
                  <option value="0">GET</option>
                  <option value="1">POST</option>
                </select>
              </div>
              <input type="url" id="webhook_url" class="form-control" name="webhook_url" v-model="form.webhook_url">
              <div class="input-group-append">
                <button id="webhook-test" class="btn btn-sm btn-inverse-secondary" type="button">
                  <i class="mdi mdi-send"></i> Test
                </button>
              </div>
            </div>
          </jet-form-line>

          <div class="form-group row">
            <label class="col-md-3 col-form-label">
              Webhook Types
              <a href="#" id="webhook-event-types-toggle" class="d-block p-3" @click.stop="toggleTypes">
                <i class="mdi mdi-checkbox-marked-outline"></i> toggle all
              </a>
            </label>
            <div class="col-md-9">
              <div class="form-check" v-for="(type, id) in types" v-if="id > 0">
                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input webhook_event_types"
                    name="webhook_event_types[]" :value="id"
                    v-model="form.webhook_event_types">
                    {{ type.name }}
                  <i class="input-helper"></i>
                </label>
              </div>
            </div>
          </div>
        </tab>
        <template #footer>
          <jet-button type="submit" :disabled="form.processing">
            Save changes
          </jet-button>

          <inertia-link class="btn btn-gradient-light" :href="route('websites.index')">
            <i class="mdi mdi-close"></i> Cancel
          </inertia-link>
        </template>
      </tabs>
    </form>
  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout';
  import Tabs from '../../Layouts/Tabs';
  import Tab from '../../Layouts/Tab';
  import JetInput from '../../Jetstream/Input'
  import JetButton from './../../Jetstream/Button'
  import JetInputUpload from './../../Jetstream/InputUpload'
  import JetFormLine from './../../Jetstream/FormLine'

  export default {
    components: {
      AppLayout,
      Tabs,
      Tab,
      JetInput,
      JetButton,
      JetInputUpload,
      JetFormLine,
    },

    props: {
      uuid: { type: String, default: null },
      name: { type: String, default: null },
      domain: { type: String, default: null },
      dedupe_subscribers: { type: Boolean, default: false },
      icon: { type: String, default: null },
      image: { type: String, default: null },
      campaigns: { type: Array, default: [] },
      webhook_method: { type: Number, default: 0 },
      webhook_url: { type: String, default: null },
      webhook_event_types: { type: Array, default: [] },
      campaignsList: { type: Array, default: [] },
      types: { type: Object, default: {} },
    },

    data() {
      return {
        form: this.$inertia.form({
          '_method': 'POST',
          uuid: this.uuid || null,
          name: this.name || null,
          domain: this.domain || null,
          dedupe_subscribers: this.dedupe_subscribers || null,
          icon: this.icon || null,
          iconupload: null,
          image: this.image || null,
          imageupload: null,
          campaigns: this.campaigns || null,
          webhook_method: parseInt(this.webhook_method) || 0,
          webhook_url: this.webhook_url || null,
          webhook_event_types: this.webhook_event_types || null
        }, {
          bag: 'websites',
          resetOnSuccess: false,
        }),
      }
    },

    methods: {
      submit() {
        if (this.uuid) {
          this.form._method = 'PUT';
          this.form.post(route('websites.update', this.uuid), { preserveScroll: true });
        } else {
          this.form.post(route('websites.store'), { preserveScroll: true });
        }
      },

      toggleCampaigns () {
        if (this.campaignsList.length == this.form.campaigns.length) {
          this.form.campaigns = [];
        } else {
          this.form.campaigns = this.campaignsList;
        }
      },

      toggleTypes () {
        var all = [];
        for (const property in this.types) {
          all.push(property);
        }

        if (all.length == this.form.webhook_event_types.length) {
          this.form.webhook_event_types = [];
        } else {
          this.form.webhook_event_types = all;
        }
      },
    },

    computed: {
      associatedCount () {
        return this.form.campaigns.length;
      },
      associated: {
        set: function(val) {
          var obj = this;
          var campaigns = [];

          val.forEach(function(v) {
            obj.campaignsList.forEach(function(camp) {
              if (v == camp.id) {
                campaigns.push(camp);
              }
            });
          });

          this.form.campaigns = campaigns;
        },
        get: function() {
          return this.form.campaigns.map(e => e.id);
        }
      },
      typesCount () {
        return this.form.webhook_event_types.length;
      },
    }
  }
</script>
