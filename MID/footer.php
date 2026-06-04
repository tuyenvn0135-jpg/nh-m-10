<footer class="bg-[#0A2540] text-gray-400 text-xs pt-12 pb-6 border-t border-slate-800 mt-20">
        <div class="container mx-auto px-4 max-w-7xl grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            
            <div>
                <h4 class="text-white font-bold text-sm mb-3">KINGFISHER SHOWROOM</h4>
                <p class="leading-relaxed mb-2">Hệ thống phân phối dụng cụ câu cá giải đấu cấp cao.</p>
                <p>📍 Địa chỉ: 123 Đường Bờ Biển, Phường 5, TP. Vũng Tàu</p>
                <p class="mt-1">📞 Hotline hỗ trợ: 1900.XXXX (8:00 - 21:00)</p>
            </div>

            <div>
                <h4 class="text-white font-bold text-sm mb-3">CHÍNH SÁCH ĐỔI TRẢ & BẢO HÀNH</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-white transition">✓ Đổi trả 1-1 nếu gãy hỏng do đơn vị vận chuyển</a></li>
                    <li><a href="#" class="hover:text-white transition">✓ Bảo hành chính hãng ổ bi máy câu trong 12 tháng</a></li>
                    <li><a href="#" class="hover:text-white transition">✓ Cam kết bồi hoàn 200% nếu phát hiện hàng giả</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-sm mb-3">ĐĂNG KÝ NHẬN TIN KHUYẾN MÃI</h4>
                <p class="mb-3">Nhận ngay mẹo chọn mồi câu và thông báo giải đấu lure sớm nhất.</p>
                <div class="flex">
                    <input type="email" placeholder="Email của bạn..." class="bg-slate-800 px-3 py-2 text-white rounded-l focus:outline-none w-full">
                    <button class="bg-[#FF9F1C] text-black font-bold px-4 rounded-r hover:bg-orange-500">Gửi</button>
                </div>
            </div>
        </div>
        
        <div class="text-center border-t border-slate-800 pt-6 text-gray-500">
            &copy; 2026 KingFisher - Thế Giới Cần Câu Cao Cấp. All rights reserved.
        </div>
    </footer>

    <div class="fixed bottom-5 right-5 z-50">
        <button id="chat-toggle-btn" class="bg-[#FF9F1C] hover:bg-orange-500 text-black font-bold p-3.5 rounded-full shadow-2xl flex items-center justify-center text-xl transition">
            💬
        </button>

        <div id="chat-box" class="hidden bg-white w-72 sm:w-80 h-96 rounded-xl shadow-2xl border border-gray-200 flex flex-col justify-between absolute bottom-16 right-0 overflow-hidden">
            <div class="bg-[#0A2540] text-white p-3 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                    <span class="text-xs font-bold tracking-wide">Trợ lý ảo KingFisher Bot</span>
                </div>
                <button id="chat-close-btn" class="text-gray-400 hover:text-white text-sm font-bold">✕</button>
            </div>

            <div id="chat-contents" class="p-4 overflow-y-auto flex-1 text-xs space-y-3 bg-gray-50">
                <div class="bg-white p-2.5 rounded-lg border border-gray-100 max-w-[85%] text-gray-800 shadow-sm">
                    Xin chào cần thủ! Tôi có thể giúp gì cho bạn về kỹ thuật câu hoặc tư vấn lên cấu hình combo cần máy hôm nay?
                </div>
            </div>

            <div class="p-2 border-t bg-white flex flex-wrap gap-1">
                <button onclick="sendQuickMessage('Cách chọn cần câu máy?')" class="bg-gray-100 hover:bg-blue-50 text-[10px] text-gray-700 px-2 py-1 rounded border">🎣 Chọn cần máy</button>
                <button onclick="sendQuickMessage('Phí ship đóng ống nhựa PVC?')" class="bg-gray-100 hover:bg-blue-50 text-[10px] text-gray-700 px-2 py-1 rounded border">📦 Phí ship PVC</button>
                <button onclick="sendQuickMessage('Hàng Shimano chính hãng không?')" class="bg-gray-100 hover:bg-blue-50 text-[10px] text-gray-700 px-2 py-1 rounded border">🛡️ Shimano</button>
            </div>

            <div class="p-2 border-t flex bg-white">
                <input type="text" id="chat-input" placeholder="Nhập câu hỏi của bạn tại đây..." class="w-full text-xs p-2 outline-none border rounded-l focus:border-slate-400" onkeypress="if(event.key === 'Enter') handleUserMessage()">
                <button onclick="handleUserMessage()" class="bg-[#0A2540] text-white px-3 text-xs font-bold rounded-r hover:bg-slate-800">Gửi</button>
            </div>
        </div>
    </div>

    <script>
        const chatToggleBtn = document.getElementById('chat-toggle-btn');
        const chatCloseBtn = document.getElementById('chat-close-btn');
        const chatBox = document.getElementById('chat-box');
        const chatContents = document.getElementById('chat-contents');
        const chatInput = document.getElementById('chat-input');

        // Bật / tắt khung chat
        chatToggleBtn.addEventListener('click', () => chatBox.classList.toggle('hidden'));
        chatCloseBtn.addEventListener('click', () => chatBox.classList.add('hidden'));

        function appendMessage(text, isUser = false) {
            const msgDiv = document.createElement('div');
            if (isUser) {
                msgDiv.className = "bg-[#0A2540] text-white p-2.5 rounded-lg max-w-[85%] ml-auto text-right shadow-sm font-medium";
            } else {
                msgDiv.className = "bg-white p-2.5 rounded-lg border border-gray-100 max-w-[85%] mr-auto text-gray-800 shadow-sm";
            }
            msgDiv.innerText = text;
            chatContents.appendChild(msgDiv);
            chatContents.scrollTop = chatContents.scrollHeight; // Cuộn tự động xuống dưới
        }

        function getBotResponse(input) {
            const cleanInput = input.toLowerCase();
            
            if (cleanInput.includes('chọn cần') || cleanInput.includes('máy câu')) {
                return "💡 Lời khuyên: Nếu bạn mới bắt đầu, nên chọn dòng cần máy đứng (Spinning) độ cứng M hoặc MH để dễ thao tác quăng mồi lure giải trí cuối tuần nhé!";
            }
            if (cleanInput.includes('pvc') || cleanInput.includes('ship') || cleanInput.includes('vận chuyển')) {
                return "📦 Hệ thống tự động tính chi phí vận chuyển theo kích thước. Các dòng sản phẩm cần câu sẽ được bọc cố định trong ống nhựa PVC cứng để tránh gãy lóng khi vận chuyển từ xa.";
            }
            if (cleanInput.includes('shimano') || cleanInput.includes('daiwa') || cleanInput.includes('chính hãng')) {
                return "🛡️ KingFisher cam kết 100% sản phẩm Shimano, Daiwa đều nhập khẩu nguyên hộp, đầy đủ giấy tờ thông số kỹ thuật minh bạch và chế độ bảo hành vàng.";
            }
            return "Cảm ơn cần thủ đã nhắn tin! Yêu cầu tư vấn setup đồ câu của bạn đã được chuyển tới nhân viên kỹ thuật trực máy, chúng tôi sẽ liên hệ lại ngay qua số hotline của bạn.";
        }

        function handleUserMessage() {
            const userText = chatInput.value.trim();
            if (!userText) return;

            appendMessage(userText, true);
            chatInput.value = '';

            // Bot phản hồi giả lập sau 600ms
            setTimeout(() => {
                const botReply = getBotResponse(userText);
                appendMessage(botReply, false);
            }, 600);
        }

        function sendQuickMessage(text) {
            appendMessage(text, true);
            setTimeout(() => {
                const botReply = getBotResponse(text);
                appendMessage(botReply, false);
            }, 500);
        }
    </script>
</body>
</html>