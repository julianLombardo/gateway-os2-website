  <section class="hero" style="padding: 8rem 3rem;">
    <div class="hero-content">
      <p class="overline">Error 500</p>
      <h1>Something <em>broke</em></h1>
      <p class="subtitle">An internal error occurred. Unlike GatewayOS2's kernel, our web server sometimes falters.</p>
      <br>
      <div class="hero-buttons">
        <a href="/" class="btn btn-primary">Back to Home</a>
        <a href="/contact" class="btn btn-secondary">Report Issue</a>
      </div>
    </div>
  </section>

  <section class="alt">
    <div class="container" style="text-align: center;">
      <div class="terminal" style="max-width: 500px;">
        <div class="terminal-header">
          <span class="terminal-dot red"></span>
          <span class="terminal-dot yellow"></span>
          <span class="terminal-dot green"></span>
        </div>
        <div class="terminal-body" style="text-align: left;">
          <span class="prompt">kernel</span> <span class="cmd">panic("unrecoverable error")</span><br>
          <span class="cmt">// In GatewayOS2, a kernel panic halts</span><br>
          <span class="cmt">// the CPU. On the web, we just show</span><br>
          <span class="cmt">// you this friendly message instead.</span><br><br>
          <span class="prompt">$</span> <span class="cmd">goto <a href="/" style="color: var(--burnt-orange);">home</a></span>
        </div>
      </div>
    </div>
  </section>
