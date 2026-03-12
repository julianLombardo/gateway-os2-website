/* GatewayOS2 — Architecture Diagram (guide.php) */

var layerData = {
  apps: {
    title: 'Applications Layer',
    content: '<p>Over 50 applications organized across 12 source files. Each app registers draw and event callbacks with the window manager. Categories include:</p>' +
      '<ul><li><strong>Productivity</strong> — Text editor, calculator, calendar, contacts, notes, mail client</li>' +
      '<li><strong>Games</strong> — Snake, Pong, Tetris, Minesweeper, Chess, 15-Puzzle, Billiards</li>' +
      '<li><strong>Sci-Fi Suite</strong> — Decrypt, Radar, Neural Net, Matrix Rain, Uplink, StarMap</li>' +
      '<li><strong>Security</strong> — GW-Cipher, GW-Fortress, GW-Sentinel, GW-NetScan, GW-Hashlab</li>' +
      '<li><strong>Development</strong> — Java IDE with editor, interpreter, and 8 sample programs</li></ul>'
  },
  gui: {
    title: 'GUI / Window Manager',
    content: '<p>The compositing window manager handles all visual output. Key components:</p>' +
      '<ul><li><strong>Compositor</strong> — Z-ordered window rendering with overlap handling</li>' +
      '<li><strong>Desktop</strong> — Event loop, context menus, wallpaper</li>' +
      '<li><strong>Dock</strong> — Right-edge pixel-art application launcher</li>' +
      '<li><strong>Menu Bar</strong> — Top-of-screen category menus for launching apps</li>' +
      '<li><strong>Font Engine</strong> — Bitmap font rendering for all text display</li></ul>' +
      '<p>All rendering goes through <code>put_pixel()</code> — direct writes to the VESA framebuffer.</p>'
  },
  services: {
    title: 'System Services',
    content: '<p>Mid-level services that bridge applications and hardware:</p>' +
      '<ul><li><strong>Clipboard</strong> — System-wide copy/paste between applications</li>' +
      '<li><strong>PE32 Loader</strong> — Parses DOS/PE headers, maps sections, resolves Win32 imports</li>' +
      '<li><strong>Win32 Shim</strong> — 60+ API functions translating Windows calls to native operations</li>' +
      '<li><strong>Java Interpreter</strong> — Recursive-descent parser with variables, control flow, arrays</li>' +
      '<li><strong>Login System</strong> — Persistent credentials with auto-fill from ATA storage</li></ul>'
  },
  net: {
    title: 'Networking Stack',
    content: '<p>A complete TCP/IP implementation built from the RFCs:</p>' +
      '<ul><li><strong>Ethernet</strong> — Frame construction, MAC addressing, EtherType dispatch</li>' +
      '<li><strong>ARP</strong> — Address resolution with cache table for IP-to-MAC mapping</li>' +
      '<li><strong>IPv4</strong> — Packet routing, header checksum, fragmentation basics</li>' +
      '<li><strong>UDP</strong> — Connectionless datagrams for DNS and DHCP</li>' +
      '<li><strong>TCP</strong> — Full state machine with 3-way handshake, data transfer, teardown</li>' +
      '<li><strong>DHCP</strong> — Automatic IP configuration on boot</li>' +
      '<li><strong>DNS</strong> — Domain name resolution for the mail client</li></ul>'
  },
  drivers: {
    title: 'Hardware Drivers',
    content: '<p>Direct hardware access with no abstraction layers:</p>' +
      '<ul><li><strong>Framebuffer</strong> — VESA VBE mode setting and linear framebuffer access (1024x768x32bpp)</li>' +
      '<li><strong>PS/2 Keyboard</strong> — IRQ1 handler, scan code translation, modifier key tracking</li>' +
      '<li><strong>PS/2 Mouse</strong> — IRQ12 handler, 3-byte packet parsing, cursor position tracking</li>' +
      '<li><strong>E1000 NIC</strong> — Intel Gigabit Ethernet driver with TX/RX descriptor ring buffers</li>' +
      '<li><strong>ATA PIO</strong> — IDE disk access for persistent storage (28-bit LBA, PIO mode)</li>' +
      '<li><strong>PCI</strong> — Bus enumeration to discover and configure hardware devices</li></ul>'
  },
  kernel: {
    title: 'Kernel Core',
    content: '<p>The foundation that everything else is built on:</p>' +
      '<ul><li><strong>GDT</strong> — Global Descriptor Table defining code/data/stack segments for protected mode</li>' +
      '<li><strong>IDT</strong> — Interrupt Descriptor Table mapping IRQs and exceptions to handler functions</li>' +
      '<li><strong>PIC</strong> — Programmable Interrupt Controller remapping to avoid CPU exception conflicts</li>' +
      '<li><strong>PMM</strong> — Physical Memory Manager using bitmap allocation from the Multiboot memory map</li>' +
      '<li><strong>Heap</strong> — Dynamic memory allocator providing malloc/free for kernel and applications</li>' +
      '<li><strong>Entry</strong> — Multiboot-compliant entry point from GRUB, stack setup, C++ handoff</li></ul>' +
      '<p>No paging, no virtual memory — the entire system runs in a flat physical address space.</p>'
  }
};

function showLayer(name) {
  var data = layerData[name];
  if (!data) return;

  // Highlight the selected layer
  document.querySelectorAll('.arch-layer').forEach(function(el) {
    el.classList.toggle('selected', el.getAttribute('data-layer') === name);
  });

  var panel = document.getElementById('layer-panel');
  var content = document.getElementById('layer-panel-content');
  content.innerHTML = '<h3>' + data.title + '</h3>' + data.content;
  panel.classList.add('open');

  // Scroll to panel
  panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function hideLayer() {
  document.getElementById('layer-panel').classList.remove('open');
  document.querySelectorAll('.arch-layer').forEach(function(el) {
    el.classList.remove('selected');
  });
}
