<section class="hero" style="padding: 5rem 3rem 2rem;">
  <div class="hero-content">
    <p class="overline">Try It</p>
    <h1>Experience<br><em>GatewayOS2</em></h1>
    <p class="subtitle">An interactive simulation of the GatewayOS2 desktop environment, running right in your browser.</p>
  </div>
</section>

<section class="dark">
  <div class="container">
    <div class="desktop-sim" id="desktop-sim">
      <div class="sim-menubar">
        <span class="sim-menu-logo">G</span>
        <span class="sim-menu-title">GatewayOS2</span>
        <div class="sim-menu-right">
          <span class="sim-clock" id="sim-clock"></span>
        </div>
      </div>
      <div class="sim-desktop" id="sim-desktop">
        <!-- Windows rendered by JS -->
      </div>
      <div class="sim-dock" id="sim-dock">
        <button class="sim-dock-item" data-app="terminal" title="Terminal">T</button>
        <button class="sim-dock-item" data-app="editor" title="Text Editor">E</button>
        <button class="sim-dock-item" data-app="calculator" title="Calculator">C</button>
        <button class="sim-dock-item" data-app="about" title="About System">?</button>
        <button class="sim-dock-item" data-app="snake" title="Snake">S</button>
      </div>
    </div>
  </div>
</section>

<!-- Network Visualizer -->
<section>
  <div class="container">
    <div class="section-header">
      <span class="overline">Networking</span>
      <h2>Watch packets flow</h2>
      <p>See how GatewayOS2's TCP/IP stack handles a connection</p>
    </div>
    <div class="network-viz" id="network-viz">
      <div class="viz-controls">
        <button class="btn btn-primary" onclick="startNetworkViz()">Start Handshake</button>
        <button class="btn btn-secondary" onclick="resetNetworkViz()">Reset</button>
      </div>
      <div class="viz-canvas">
        <div class="viz-node viz-client"><span>Client</span></div>
        <div class="viz-path" id="viz-path"></div>
        <div class="viz-node viz-server"><span>Server</span></div>
      </div>
      <div class="viz-log" id="viz-log"></div>
    </div>
  </div>
</section>

<!-- Crypto Playground -->
<section class="alt">
  <div class="container">
    <div class="section-header">
      <span class="overline">Cryptography</span>
      <h2>Encrypt &amp; decrypt</h2>
      <p>Try the cipher algorithms built into GatewayOS2</p>
    </div>
    <div class="crypto-playground" id="crypto-playground">
      <div class="crypto-controls">
        <select id="cipher-select" class="form-select" onchange="updateCrypto()">
          <option value="caesar">Caesar Cipher</option>
          <option value="vigenere">Vigenere Cipher</option>
          <option value="xor">XOR Cipher</option>
          <option value="base64">Base64</option>
        </select>
        <div class="form-group" id="key-group">
          <label for="cipher-key">Key</label>
          <input type="text" id="cipher-key" value="3" placeholder="Enter key" oninput="updateCrypto()">
        </div>
      </div>
      <div class="crypto-io">
        <div class="form-group">
          <label>Plaintext</label>
          <textarea id="crypto-input" rows="3" placeholder="Type your message here..." oninput="updateCrypto()">Hello, GatewayOS2!</textarea>
        </div>
        <div class="crypto-arrow">&#x2192;</div>
        <div class="form-group">
          <label>Ciphertext</label>
          <textarea id="crypto-output" rows="3" readonly></textarea>
        </div>
      </div>
    </div>
  </div>
</section>
