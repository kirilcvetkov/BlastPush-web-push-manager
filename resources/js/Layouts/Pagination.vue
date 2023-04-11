<template>
  <ul role="menubar" aria-disabled="false" aria-label="Pagination" class="pagination b-pagination">
    <template v-for="(link, key) in links">
      <li role="presentation" aria-hidden="true" class="page-item" :class="{'disabled': ! link.url, 'active': link.active}" >
        <span v-if="! link.url" role="menuitem" :aria-label="link.label" aria-disabled="true" class="page-link" v-html="link.label"></span>
        <inertia-link v-else :key="key" role="menuitemradio" :aria-label="link.label"
          :aria-checked="{'true': link.active, 'false': ! link.active}" :aria-posinset="key" :aria-setsize="links.length"
          :tabindex="{'0': link.active}" target="_self" :href="link.url" class="page-link text-nowrap" v-html="link.label"></inertia-link>
      </li>
    </template>
  </ul>
</template>

<!--
<ul role="menubar" aria-disabled="false" aria-label="Pagination" class="pagination b-pagination">
  <li role="presentation" aria-hidden="true" class="page-item disabled">
    <span role="menuitem" aria-label="Go to first page" aria-disabled="true" class="page-link">«</span>
  </li>
  <li role="presentation" aria-hidden="true" class="page-item disabled">
    <span role="menuitem" aria-label="Go to previous page" aria-disabled="true" class="page-link">‹</span>
  </li>
  <li role="presentation" class="page-item active">
    <a role="menuitemradio" aria-label="Go to page 1" aria-checked="true" aria-posinset="1" aria-setsize="4" tabindex="0" target="_self" href="#" class="page-link">1</a>
  </li>
  <li role="presentation" class="page-item">
    <a role="menuitemradio" aria-label="Go to page 2" aria-checked="false" aria-posinset="2" aria-setsize="4" tabindex="-1" target="_self" href="#" class="page-link">2</a>
  </li>
  <li role="presentation" class="page-item"><a role="menuitemradio" aria-label="Go to page 3" aria-checked="false" aria-posinset="3" aria-setsize="4" tabindex="-1" target="_self" href="#" class="page-link">3</a></li>
  <li role="presentation" class="page-item bv-d-xs-down-none"><a role="menuitemradio" aria-label="Go to page 4" aria-checked="false" aria-posinset="4" aria-setsize="4" tabindex="-1" target="_self" href="#" class="page-link">4</a></li>
  <li role="presentation" class="page-item"><a role="menuitem" tabindex="-1" aria-label="Go to next page" target="_self" href="#" class="page-link">›</a></li>
  <li role="presentation" class="page-item"><a role="menuitem" tabindex="-1" aria-label="Go to last page" target="_self" href="#" class="page-link">»</a></li>
</ul> -->
<script>
  export default {
    props: {
      links: Array,
    },
  }
</script>
