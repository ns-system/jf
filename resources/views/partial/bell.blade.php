<style type="text/css">
  @keyframes notification {
    0%  { transform: rotateZ(0deg); }
    10% { transform: rotateZ(20deg); }
    20% { transform: rotateZ(0deg); }
    80% { transform: rotateZ(0deg); }
    90% { transform: rotateZ(20deg); }
    100%{ transform: rotateZ(0deg); }
  }
  .not-accept {
    animation: notification 2s infinite;
  }
</style>

<span class="glyphicon glyphicon-bell text-danger not-accept" aria-hidden="true"></span>