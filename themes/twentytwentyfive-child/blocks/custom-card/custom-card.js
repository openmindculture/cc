wp.blocks.registerBlockVariation(
  'core/group', {
    name: 'custom-card',
    title: 'custom Karte',
    attributes: {
      className: 'wp-block-custom-card',
      layout: { type: 'flex', orientation: 'responsive' },
      tagName: 'div'
    },
    scope: [ 'inserter', 'transform' ],
    isActive: ['className', 'layout', 'tagName' ],
    icon: 'tablet',
  }
);
