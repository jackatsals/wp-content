import Plyr from 'plyr';

const initPlayers = () => {
  const players = document.querySelectorAll('.unity-video__player');
  players.forEach(player => {
    new Plyr(player);
  });
}

const flBuilderLayout = document.querySelector('.fl-builder-content');

document.addEventListener('DOMContentLoaded', () => {
  initPlayers();
});

flBuilderLayout.addEventListener('fl-builder.preview-rendered', () => {
  initPlayers();
});

flBuilderLayout.addEventListener('fl-builder.layout-rendered', () => {
  initPlayers();
});
