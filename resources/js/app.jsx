import './bootstrap'
import React from 'react'
import { createRoot } from 'react-dom/client'
import PagesFlow from './pages/PagesFlow'
import 'reactflow/dist/style.css'

document.querySelectorAll('[data-react-pages-flow]').forEach(el => {
  const websiteId = el.dataset.websiteId

  createRoot(el).render(
    <PagesFlow websiteId={websiteId} />
  )
})
