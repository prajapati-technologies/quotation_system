<div x-data="signaturePad" style="width: 100%;">
    <div style="margin-bottom: 15px;">
        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1e293b;">Draw your signature below:</label>
        <canvas 
            x-ref="canvas" 
            width="750" 
            height="200"
            style="border: 2px dashed #94a3b8; border-radius: 8px; width: 100%; height: 200px; cursor: crosshair; background: white; display: block;">
        </canvas>
    </div>
    
    <div style="display: flex; gap: 10px; margin-top: 15px;">
        <button 
            type="button" 
            @click="clear()" 
            style="background: #ef4444; color: white; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
            Clear Signature
        </button>
    </div>
    
    <input type="hidden" name="signature" x-ref="signatureInput">

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('signaturePad', () => ({
                signaturePad: null,
                init() {
                    const canvas = this.$refs.canvas;
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    
                    // Set canvas size for high DPI displays
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    
                    this.signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)'
                    });
                    
                    this.signaturePad.addEventListener("endStroke", () => {
                        this.$refs.signatureInput.value = this.signaturePad.toDataURL();
                        this.$refs.signatureInput.dispatchEvent(new Event('input'));
                    });
                },
                clear() {
                    this.signaturePad.clear();
                    this.$refs.signatureInput.value = '';
                    this.$refs.signatureInput.dispatchEvent(new Event('input'));
                }
            }))
        })
    </script>
</div>
