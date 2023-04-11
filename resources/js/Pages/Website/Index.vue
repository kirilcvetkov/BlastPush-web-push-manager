<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>

    <tabs id="websites" v-on:selected="updatePageTitle">
      <tab name="Websites" icon="mdi-web">
        <div class="row">
          <div class="col-sm-6">
            <inertia-link class="btn btn-sm btn-gradient-success" :href="route('websites.create')">
              <i class="mdi mdi-plus"></i> Create
            </inertia-link>
            <!-- TODO <inertia-link class="btn btn-sm btn-gradient-light" :href="route('websites.index', 'trashed=1')">
              <i class="mdi mdi-delete"></i> Trashed
            </inertia-link> -->
          </div>
          <div class="col-sm-6">
            <pagination :links="$page.websites.links" class="d-flex justify-content-end" />
          </div>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th class="d-none d-xl-table-cell"> </th>
                <th>Name</th>
                <th>Domain</th>
                <th class="text-center">
                  <span class="d-xl-none" v-tooltip="'Campaigns'"><i class="mdi mdi-group text-primary"></i></span>
                  <span class="d-none d-xl-inline-block text-gradient-primary">Campaigns</span>
                </th>
                <th class="text-center">
                  <span class="d-xl-none" v-tooltip="'Subscribers'"><i class="mdi mdi-account-plus text-success"></i></span>
                  <span class="d-none d-xl-inline-block text-gradient-success">Subscribers</span>
                </th>
                <th class="text-center">
                  <span class="d-xl-none" v-tooltip="'Unsubscribers'"><i class="mdi mdi-account-minus text-danger"></i></span>
                  <span class="d-none d-xl-inline-block text-gradient-danger">Unsubscribers</span>
                </th>
                <th class="text-center">
                  <span class="d-xl-none" v-tooltip="'Sent'"><i class="mdi mdi-send text-info"></i></span>
                  <span class="d-none d-xl-inline-block text-gradient-info">Sent</span>
                </th>
                <th>Webhook</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="website in $page.websites.data">
                <td>
                  <button type="button" class="btn btn-icon-sm btn-inverse-primary"
                    @click="buildScriptsModal(website.name, website.uuid)">
                    <i class="mdi mdi-script"></i>
                  </button>
                  <button type="button" class="btn btn-icon-sm btn-inverse-success"
                    @click="buildKeysModal(website.name, website.uuid)">
                    <i class="mdi mdi-key"></i>
                  </button>
                  <inertia-link class="btn btn-inverse-info btn-icon-sm" :href="route('websites.edit', website.uuid)">
                    <i class="mdi mdi-lead-pencil"></i>
                  </inertia-link>
                  <button class="d-inline-block btn btn-inverse-danger btn-icon-sm" tabindex="-1" type="button" @click="destroy(route('websites.destroy', website.uuid))">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </td>
                <td class="d-none d-xl-table-cell">
                  <img class="img-fluid rounded" v-show="website.icon || website.image" :src="website.icon || website.image" />
                </td>
                <td>{{ website.name }}</td>
                <td>{{ website.domain }}</td>
                <td class="text-center">{{ website.campaigns_count }}</td>
                <td class="text-center">{{ website.subs_count }}</td>
                <td class="text-center">{{ website.unsubs_count }}</td>
                <td class="text-center">{{ website.pushes_count }}</td>
                <td>
                  <div class="text-success" v-if="website.webhook_event_types.length && website.webhook_url">Enabled</div>
                  <div class="text-secondary" v-else>Disabled</div>
                  <div class="small text-muted pt-1">{{ website.webhook_event_types.length }} events</div>
                </td>
                <td>
                  <div class="text-nowrap" v-tooltip="website.updated">
                    {{ website.updatedHuman }}
                  </div>
                  <div class="text-nowrap small text-muted pt-1" v-tooltip="website.created">
                    Created {{ website.createdHuman }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="row">
          <div class="offset-sm-6 col-sm-6">
            <pagination :links="$page.websites.links" class="d-flex pt-3 justify-content-end" />
          </div>
        </div>
      </tab>

      <tab name="Popup Dialogs" icon="mdi-window-maximize">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th> </th>
                <th class="d-none d-md-table-cell"> </th>
                <th>Message</th>
                <th class="text-center">Show</th>
                <th class="text-center">Delay</th>
                <th class="text-center">Websites</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="dialog in $page.dialogs.data">
                <td>
                  <button type="button" class="btn btn-inverse-primary btn-icon-sm preview"
                    :data-route="route('dialogs.preview.id', dialog.id)">
                    <i class="mdi mdi-eye-outline"></i>
                  </button>
                  <inertia-link class="btn btn-inverse-info btn-icon-sm" :href="route('dialogs.edit', dialog.id)">
                    <i class="mdi mdi-lead-pencil"></i>
                  </inertia-link>
                  <span v-if="dialog.is_global" class="btn btn-inverse-success btn-icon-sm" href="#" title="Global dialog" @click.prevent="msg('This is the global dialog')">
                    <i class="mdi mdi-web"></i>
                  </span>
                  <button v-else class="d-inline-block btn btn-inverse-danger btn-icon-sm" tabindex="-1" type="button" @click="destroy(route('dialogs.destroy', dialog.id))">
                    <i class="mdi mdi-delete"></i>
                  </button>
                </td>
                <td class="d-none d-md-table-cell">
                  <img class="img-fluid rounded" :src="dialog.image || dialog.image_default" /></td>
                <td>{{ dialog.message }}</td>
                <td class="text-center">
                  <span :title="'Dialog pop will be shown ' + dialog.show_percentage + '% of the time'" data-toggle="tooltip">
                    {{ dialog.show_percentage }}%
                  </span>
                </td>
                <td class="text-center">
                  <span :title="'Dialog pop will be delayed by ' + dialog.delay + ' seconds'" data-toggle="tooltip">
                    {{ dialog.delay }}s
                  </span>
                </td>
                <td class="text-center">
                  <span title="Websites associalted with this dialog" data-toggle="tooltip">{{ dialog.websites_count }}</span>
                </td>
                <td>
                  <span class="d-block text-nowrap" v-tooltip="dialog.updated">
                    {{ dialog.updatedHuman }}
                  </span>
                  <span class="d-block text-nowrap small text-muted pt-1" v-tooltip="dialog.created">
                    Created {{ dialog.createdHuman }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </tab>
    </tabs>

    <modal v-if="scriptsModal" @close="scriptsModal = false" v-on:keyup="keyboardEvent">
      <template #title>{{ scriptsModalTitle }} Scripts</template>
      <div class="card-body">
        <legend class="text-muted display-4">push.js</legend>
        <p class="mb-2">Front-end JavaScript file - Place the following code in the <b>&lt;head&gt;</b> section of your page:</p>
        <code class="p-0 text-dark">
            &lt;head&gt;<br/>
            ...<br/>
            <span class="text-info">&lt;script type="text/javascript" src="push.js"&gt;&lt;/script&gt;</span><br/>
            ...<br/>
            &lt;/head&gt;
        </code>
        <p class="mt-3">If you would like to pass back custom subscriber variables,
          head over to the <a :href="route('variables.index')">Variables</a> page
          and navigate to the <b>How to use variables</b> tab for more information.
        </p>
      </div>
      <div class="card-body">
        <legend class="text-muted display-4">sw.js</legend>
        <p>Service worker JavaScript file - This code manages push notifications in the background of user's browser. This file must be placed in the root folder of your website.</p>
      </div>
      <template #footer>
        <a class="btn btn-gradient-primary" :href="scriptsModalHref" download ref="actionButton" autofocus>
          <i class="mdi mdi-download"></i></i> Download
        </a>
      </template>
    </modal>

    <modal v-if="keysModal" @close="keysModal = false" v-on:keyup="keyboardEvent">
      <template #title>{{ keysModalTitle }} Website Key</template>
      <div class="card-body"><code class="text-success">{{ keysModalUuid }}</code></div>
    </modal>
  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout';
  import Tabs from '../../Layouts/Tabs';
  import Tab from '../../Layouts/Tab';
  import Pagination from '../../Layouts/Pagination'
  import Modal from '../../Layouts/Modal'

  export default {
    components: {
      AppLayout,
      Tabs,
      Tab,
      Pagination,
      Modal,
    },

    methods: {
      updatePageTitle(title) {
        this.pageTitle = title;
      },
      buildScriptsModal(title, uuid) {
        this.scriptsModalHref = route('scripts.download', uuid);
        this.scriptsModalTitle = title;
        this.scriptsModal = true;
      },
      buildKeysModal(title, uuid) {
        this.keysModalTitle = title;
        this.keysModalUuid = uuid;
        this.keysModal = true;
      },
      keyboardEvent (e) {
        if (e.which === 27) {
          this.scriptsModal = false;
          this.keysModal = false;
        }
      },
      destroy(route) {
        if (confirm('Are you sure?')) {
          this.$inertia.delete(route);
        }
      },
      msg(msg) {
        alert(msg);
      }
    },

    data() {
      return {
        pageTitle: 'Websites',
        tab: this.$page.tab || 'websites',
        scriptsModal: false,
        scriptsModalHref: '',
        scriptsModalTitle: '',
        keysModal: false,
        keysModalUuid: '',
        keysModalTitle: '',
      }
    }
  }
</script>
