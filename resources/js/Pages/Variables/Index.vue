<template>
  <app-layout>
    <template #title>{{ pageTitle }}</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>

    <div class="card">
      <div class="card-body">
        <div class="card-description">
          <h2 class="mb-3">How to use
            <a href='#' @click.prevent="collapseDetails"><i class="mdi mdi-chevron-down"></i></a>
          </h2>
          <div v-show="toggleDetails">
            <h3 class="text-info">1. Message link</h3>
            <p class="pt-2">Use the following list of variables when building your message link to insert values based on either one of the scopes.</p>
            <p class="pl-3">Example:
              <code class="text-dark">
                https://www.example.com/test.php?website=<span class="text-danger">{$website_id}</span>&browser=<span class="text-danger">{$subscriber_browser}</span>&campaign=<span class="text-danger">{$campaign_name}</span>
              </code>
            </p>
            <p>You can specify a default value for custom variables to fall back on like this:
              <code class="text-dark">
                {$scope_custom_variable<span class="text-muted font-weight-bold">|</span><span class="text-danger">default</span>}
              </code>
            </p>

            <h3 class="text-info">2. Website setup for custom subscriber variables</h3>
            <p class="pt-2 mb-0">Use the following code to setup custom subscriber variables on your website. Place it within the <b>&lt;head&gt;</b> section of the HTML document.</p>
            <p>Make sure to replace the website key and variables' sample values in the JSON code with user-specific values.</p>
            <pre>
            &lt;script type=&quot;text/javascript&quot;&gt;
                (function(document, window) {
                    var script = document.createElement(&#039;script&#039;);
                    script.src = &#039;{{ $page.route }}&#039;;
                    script.onload = function() {
                        <span><span class="text-danger">document.bpVars</span> = <span class="text-info">{{ $page.example }};</span></span>
                    }
                    document.getElementsByTagName(&#039;head&#039;)[0].appendChild( script );
                })(document, window);
            &lt;/script&gt;
            </pre>
          </div>
        </div>

        <alert key="key" message="Custom subscriber variables coming soon!" type="alert-warning"></alert>

        <table class="table mb-5">
          <thead>
            <tr>
              <th>Scope</th>
              <th>Variable</th>
              <th>Type</th>
              <th>Template String</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in payload">
              <td class="p-0">
                <button class="btn btn-sm" :class="row.btn">
                  {{ row.scope }}
                </button>
              </td>
              <td>{{ row.name }}</td>
              <td :class="row.color">
                  {{ row.type }}
              </td>
              <td>{{ row.var }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout';
  import Alert from '../../Layouts/Alert';

  export default {
    components: {
      AppLayout,
      Alert,
    },

    methods: {
      collapseDetails() {
        this.toggleDetails = ! this.toggleDetails;
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
        pageTitle: 'Variables',
        page: this.$page.page,
        payload: this.$page.payload,
        toggleDetails: false,
      }
    }
  }
</script>
