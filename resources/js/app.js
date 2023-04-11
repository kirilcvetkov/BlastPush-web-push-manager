require('./bootstrap');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import VueCookies from 'vue-cookies';

Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(VueCookies);

const bsTooltip = (el, binding) => {
  const t = []

  if (binding.modifiers.focus) t.push('focus');
  if (binding.modifiers.hover) t.push('hover');
  if (binding.modifiers.click) t.push('click');
  if (!t.length) t.push('hover');
  $(el).tooltip({
    title: binding.value || '',
    placement: binding.arg || 'top',
    trigger: t.join(' '),
    html: !!binding.modifiers.html,
  });
}

Vue.directive('tooltip', {
  bind: bsTooltip,
  update: bsTooltip,
  unbind (el) {
    $(el).tooltip('dispose')
  }
});

const app = document.getElementById('app');

new Vue({
  render: (h) =>
  h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: (name) => require(`./Pages/${name}`).default,
    },
  }),
}).$mount(app);
