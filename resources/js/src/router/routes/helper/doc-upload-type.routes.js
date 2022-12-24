import { PERMISSION_BITMASK_DOC_UPLOAD_TYPES } from '@/utils/permissions'

export default [
  {
    path: '/helper/document-upload-type/:mode?',
    name: 'helper-document-upload-type-list',
    component: () => import('@/views/pages/helper/doc-upload-type/List.vue'),
    meta: {
      permissions: [PERMISSION_BITMASK_DOC_UPLOAD_TYPES],
    },
  },
]
