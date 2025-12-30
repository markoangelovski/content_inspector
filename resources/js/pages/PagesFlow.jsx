import React, { useEffect, useState } from 'react'
import ReactFlow, { Background, Controls} from 'reactflow'

function PagesFlowComponent({ websiteId }) {
  const [nodes, setNodes] = useState([])
  const [edges, setEdges] = useState([])

  useEffect(() => {
    // Initial state: homepage only
    setNodes([
      {
        id: 'home',
        position: { x: 0, y: 0 },
        data: { label: 'Homepage' },
      },
    ])
  }, [])

  return (
    <div className="h-[500px] w-full bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-xl">
      <ReactFlow
        nodes={nodes}
        edges={edges}
        fitView
      >
        <Background />
        <Controls />
      </ReactFlow>
    </div>
  )
}

export default React.memo(PagesFlowComponent);