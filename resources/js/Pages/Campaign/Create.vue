<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon><i class="mdi mdi-group mdi-18px"></i></template>

    <form method="POST" action="" @submit.prevent="submit" @keydown="$page.errors = {}">
      <tabs>
        <tab name="Campaign" icon="mdi-web" :selected="true">
          <jet-input id="name" type="text" label="Name" :error="$page.errors.name" v-model="form.name" autofocus>
            <div class="d-flex">
              <div class="form-check form-check-success">
                <label class="form-check-label w-25 text-nowrap">
                  <input type="radio" name="enabled" class="form-check-input" value="1" :checked="form.enabled">
                  Enabled
                  <i class="input-helper"></i>
                </label>
              </div>
              <div class="form-check form-check-danger ml-5">
                <label class="form-check-label w-25 text-nowrap">
                  <input type="radio" name="enabled" class="form-check-input" value="0" :checked="! form.enabled">
                  Disabled
                  <i class="input-helper"></i>
                </label>
              </div>
            </div>
          </jet-input>

          <jet-form-line id="schedules" :label="waterfall ? 'Schedules' : 'Schedule'">
            <button v-if="waterfall" type="button" class="btn btn-inverse-info btn-icon-sm ml-2" @click="addSchedule">
              <i class="mdi mdi-plus"></i>
            </button>
          </jet-form-line>

          <ul id="schedules" class="list-group list-group-flush mb-4">
            <li class="list-group-item" v-for="(schedule, index) in form.schedules" :key="schedule.id">
              <div class="input-group">
                <div class="input-group-prepend">
                  <button class="schedules-handle btn btn-inverse-secondary btn-rounded btn-icon" style="cursor: row-resize;">
                    <i class="mdi mdi-drag-vertical"></i>
                  </button>
                </div>
                <div class="col-sm p-0">
                  <select v-model="schedule.message_id" class="form-control form-control-sm" required>
                    <option v-for="message in messages" :value="message.id">
                      {{ message.title }}
                    </option>
                  </select>
                  <small class="form-text text-muted mb-1">
                    Message to send
                  </small>
                </div>

                <div v-if="waterfall" class="col-sm-3 p-0">
                  <input type="number" class="form-control form-control-sm delay" v-model="schedule.delay" required>
                  <small class="form-text text-muted">Delay in minutes between messages</small>
                </div>
                <div v-if="waterfall" class="col-sm-2 p-0">
                  <input type="number" class="form-control form-control-sm" v-model="totalDelay" disabled>
                  <small class="form-text text-muted">Total delay after subscription</small>
                </div>

                <div v-if="waterfall" class="input-group-append">
                  <button type="button" class="btn btn-inverse-danger btn-rounded btn-icon" @click="deleteSchedule(index)">
                    <i class="mdi mdi-minus"></i>
                  </button>
                </div>

                <div v-if="scheduled" class="col-sm-2 p-0">
                  <datetime v-model="schedule.scheduled_at" type="datetime" input-class="form-control form-control-sm" value-zone="local" :use12-hour="use12Hour"></datetime>
                  <small class="form-text text-muted">Total delay after subscription</small>
                </div>

                <div v-if="reoccurring" class="col-sm-6 p-0 d-flex">
                  <select v-model="schedule.reoccurring_frequency" class="form-control form-control-sm">
                    <option value="hourly">Hourly</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                  </select>

                  <select v-if="schedule.reoccurring_frequency == 'hourly'" v-model="schedule.hour_minute" class="form-control form-control-sm">
                    <option v-for="n in 60" :value="'00:' + (n - 1)">:{{ String(n - 1).padStart(2, '0') }} minute</option>
                  </select>

                  <select v-if="schedule.reoccurring_frequency == 'monthly'" v-model="schedule.day" class="form-control form-control-sm">
                    <option value="1">1st</option>
                    <option value="2">2nd</option>
                    <option value="3">3rd</option>
                    <option value="4">4th</option>
                    <option value="5">5th</option>
                    <option value="6">6th</option>
                    <option value="7">7th</option>
                    <option value="8">8th</option>
                    <option value="9">9th</option>
                    <option value="10">10th</option>
                    <option value="11">11th</option>
                    <option value="12">12th</option>
                    <option value="13">13th</option>
                    <option value="14">14th</option>
                    <option value="15">15th</option>
                    <option value="16">16th</option>
                    <option value="17">17th</option>
                    <option value="18">18th</option>
                    <option value="19">19th</option>
                    <option value="20">20th</option>
                    <option value="21">21st</option>
                    <option value="22">22nd</option>
                    <option value="23">23rd</option>
                    <option value="24">24th</option>
                    <option value="25">25th</option>
                    <option value="26">26th</option>
                    <option value="27">27th</option>
                    <option value="28">28th</option>
                    <option value="29">29th</option>
                    <option value="30">30th</option>
                    <option value="31">31st</option>
                  </select>

                  <select v-if="schedule.reoccurring_frequency == 'weekly'" v-model="schedule.day" class="form-control form-control-sm">
                    <option value="0">Monday</option>
                    <option value="1">Tuesday</option>
                    <option value="2">Wednesday</option>
                    <option value="3">Thursday</option>
                    <option value="4">Friday</option>
                    <option value="5">Saturday</option>
                    <option value="6">Sunday</option>
                  </select>

                  <datetime v-if="schedule.reoccurring_frequency == 'daily' || schedule.reoccurring_frequency == 'weekly' || schedule.reoccurring_frequency == 'monthly'"
                    v-model="schedule.hour_minute" type="time" input-class="form-control form-control-sm"
                    value="local" value-zone="local" :use12-hour="use12Hour"></datetime>
                </div>
              </div>
            </li>
          </ul>
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

        <alert key="key" message="Triggers to subscriber actions coming soon!" type="alert-warning"></alert>

        <jet-button type="submit" :disabled="form.processing">Save changes</jet-button>
        <inertia-link class="btn btn-gradient-light" :href="route('campaigns.index')">
          <i class="mdi mdi-close"></i> Cancel
        </inertia-link>
      </tabs>
    </form>
  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout';
  import Tabs from '../../Layouts/Tabs';
  import Tab from '../../Layouts/Tab';
  import JetInput from '../../Jetstream/Input'
  import JetFormLine from './../../Jetstream/FormLine'
  import JetButton from './../../Jetstream/Button'
  import { Datetime } from 'vue-datetime';
  import 'vue-datetime/dist/vue-datetime.css'
  import Alert from '../../Layouts/Alert';

  export default {
    components: {
      AppLayout,
      Tabs,
      Tab,
      JetInput,
      JetFormLine,
      JetButton,
      Datetime,
      Alert,
    },

    props: {
      id: { type: Number, default: null },
      name: { type: String, default: null },
      enabled: { type: Number, default: 1 },
      type: { type: String, default: null },
      websites: { type: Array, default() { return [] } },
      websitesList: { type: Array, default() { return [] } },
      schedules: { type: Array, default() { return [] } },
      messages: { type: Array, default() { return [] } },
    },

    data() {
      return {
        pageTitle: 'Campaigns',
        waterfall: this.type == 'waterfall' || false,
        scheduled: this.type == 'scheduled' || false,
        reoccurring: this.type == 'reoccurring' || false,
        use12Hour: true,
        form: this.$inertia.form({
          '_method': 'POST',
          name: this.name,
          enabled: this.enabled,
          type: this.type,
          websites: this.websites || [],
          schedules: this.schedules || [],
        }, {
          bag: 'campaigns',
          resetOnSuccess: false,
        })
      }
    },

    methods: {
      addSchedule: function(index) {
        this.schedules.push({
          campaign_id: null,
          day: null,
          delay: 0,
          hour_minute: null,
          is_trigger: null,
          message_id: null,
          order: null,
          reoccurring_frequency: null,
          scheduled_at: null,
          trigger_schedule_id: null,
        });
      },
      deleteSchedule: function(index) {
        if (confirm("Are you sure?")) {
          this.schedules.splice(index, 1);
        }
      },
      submit() {
        if (this.id) {
          this.form._method = 'PUT';
          this.form.post(route('campaigns.update', this.id), { preserveScroll: true });
        } else {
          this.form.post(route('campaigns.store'), { preserveScroll: true });
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
      totalDelay () {
        var total = 0;

        this.schedules.forEach(function(schedule) {
          if (schedule.delay) {
            total += parseInt(schedule.delay);
          }
        });

        return total;
      },
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
