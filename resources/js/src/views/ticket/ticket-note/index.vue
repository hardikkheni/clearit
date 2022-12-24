<template>
  <div class="ticket-message-card">
    <b-card bg-variant="transparent" class="chat-widget mb-0" no-body border-variant="primary">
      <b-card-title>
        <feather-icon size="30" icon="TrelloIcon" />
        Notes
      </b-card-title>
      <hr />
      <section class="chat-app-window">
        <vue-perfect-scrollbar ref="refNotePs" :settings="perfectScrollbarSettings" class="user-chats scroll-area">
          <b-list-group class="mt-1">
            <b-list-group-item v-for="(note, i) of notes" :key="i">
              <div class="clearfix">
                <small class="float-left text-primary">
                  {{ (note.user && note.user.firstname) || 'Unknown' }} at {{ note.createdOn | dateFormat(DATE_FORMAT) }}
                </small>
                <small class="float-right text-primary">{{ note.createdOn | dateFormat(HOUR_FORMAT) }} </small>
              </div>
              <div v-if="note.description" v-html="decodeHtmlSpecialChars(note.description)" />
              <a v-if="note.notefile" :href="note.file_url" download>
                <b-badge variant="light-primary"><feather-icon icon="DownloadIcon" /> {{ note.notefile }} </b-badge>
              </a>
            </b-list-group-item>
          </b-list-group>
        </vue-perfect-scrollbar>
        <validation-observer ref="noteForm" #default="{ invalid }">
          <b-form @submit.prevent="submit">
            <validation-provider #default="{ errors }" name="Message" vid="description" rules="required">
              <quill-editor v-model="form.description" :options="editorOptions" />
              <div id="note-editor-toolbar" class="clearfix border-bottom-0">
                <template v-if="errors[0]">
                  <small class="text-danger">{{ errors[0] }}</small> <br />
                </template>
                <button class="ql-bold" />
                <button class="ql-italic" />
                <button class="ql-underline" />
                <button class="ql-strike" />
                <button class="ql-list" value="ordered" />
                <button class="ql-list" value="bullet" />
                <button class="ql-link" />
                <button class="ql-align" value="center" />
                <button class="ql-align" value="right" />
                <button class="ql-align" value="justify" />
                <button class="ql-blockquote" />
                <button class="ql-code-block" />
                <button class="ql-clean" />
                <b-form-file ref="fileInput" v-model="form.notefile" type="hidden" plain />
                <b-button type="submit" variant="flat-primary" class="file-label float-right" :disabled="invalid">
                  <feather-icon icon="SendIcon" />
                </b-button>
                <b-button variant="flat-primary" class="file-label float-right mr-05" dadge @click.prevent="triggerFileInput">
                  <feather-icon icon="PaperclipIcon" :badge="fileName.fullName" />
                </b-button>
              </div>
            </validation-provider>
          </b-form>
        </validation-observer>
      </section>
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardTitle, BButton, BFormFile, VBTooltip, BListGroup, BListGroupItem, BBadge, BForm } from 'bootstrap-vue'
import { mapActions, mapMutations } from 'vuex'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { quillEditor } from 'vue-quill-editor'
import { ValidationProvider, ValidationObserver } from 'vee-validate'

import { DATE_FORMAT, HOUR_FORMAT } from '@/utils/config'
import { dateFormat, decodeHtmlSpecialChars } from '@/utils/filters'

import { apiResponseWrapper } from '@/libs/api.handler'

export default {
  components: {
    BListGroup,
    BListGroupItem,
    BCard,
    BCardTitle,
    VuePerfectScrollbar,
    quillEditor,
    BButton,
    BFormFile,
    BBadge,
    ValidationProvider,
    ValidationObserver,
    BForm,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  filters: { dateFormat },
  props: {
    notes: {
      type: Array,
      required: true,
    },
    ticket: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      DATE_FORMAT,
      HOUR_FORMAT,
      perfectScrollbarSettings: {
        // maxScrollbarLength: 150,
        wheelPropagation: false,
      },
      noteInputMessage: null,
      editorOptions: {
        modules: {
          toolbar: '#note-editor-toolbar',
        },
        placeholder: 'Message',
      },
      form: {
        description: null,
        notefile: null,
      },
    }
  },
  computed: {
    fileName() {
      return {
        name: this.form.notefile ? `${this.form.notefile.name?.substr(0, 15)}${this.form.notefile.name?.length > 15 ? '...' : ''}` : '',
        fullName: this.form.notefile ? `${this.form.notefile.name}` : '',
      }
    },
  },
  methods: {
    decodeHtmlSpecialChars,
    ...mapActions('note', { createNote: 'create' }),
    ...mapMutations('ticket', { addNote: 'addNote' }),
    triggerFileInput() {
      this.$refs.fileInput.$el.click()
    },
    submit() {
      apiResponseWrapper(
        this,
        async () => {
          const res = await this.createNote({ ...this.form, ticketId: this.ticket.id })
          this.addNote(res.data)
          this.form.description = null
          this.form.notefile = null
          this.$refs.noteForm.reset()
          this.$refs.refNotePs.$el.scrollTo({ top: 0, behavior: 'smooth' })
          return res
        },
        this.$refs.noteForm,
      )
    },
  },
}
</script>

<style lang="scss">
@import '~@core/scss/vue/libs/quill.scss';
@import '@core/scss/base/pages/app-chat-list.scss';
</style>

<style lang="scss" scoped>
@import '~@core/scss/base/bootstrap-extended/include';

.ticket-message-card ::v-deep {
  #note-editor-toolbar {
    border: 1px solid $primary !important;
    border-top: unset !important;
    border-radius: 0rem 0rem 0.5rem 0.5rem;
    .file-label {
      width: unset;
    }
  }
  .quill-editor {
    .ql-container.ql-snow {
      border: 1px solid $primary !important;
      border-bottom: unset !important;
      border-radius: 0.5rem 0.5rem 0rem 0rem;
    }
  }
  .form-control-file {
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
    z-index: -1;
  }
  .mr-05 {
    margin-right: 0.5rem;
  }
}
</style>
