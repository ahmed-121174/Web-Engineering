class QuoteGenerator {
    #apiKey;
    #apiUrl;

    constructor() {
        this.#apiKey = "X0at6UOjocKjiB0rnbBegA==LBagYKFPp5dTTeUQ"; // Private API Key
        this.#apiUrl = "https://api.api-ninjas.com/v1/quotes";
    }

    fetchQuote = async () => {
        try {
            const response = await fetch(this.#apiUrl, {
                headers: { 'X-Api-Key': this.#apiKey }
            });
            const data = await response.json();
            return data[0].quote;
        } catch (error) {
            return "Error fetching quote. Please try again.";
        }
    };
}

class QuoteApp {
    constructor() {
        this.quoteBox = document.getElementById('quote-box');
        this.wordCount = document.getElementById('word-count');
        this.charCount = document.getElementById('char-count');
        this.vowelCount = document.getElementById('vowel-count');
        this.chatWindow = document.getElementById('chat-window');
        this.chatBtn = document.getElementById('chat-btn');
        this.quoteGenerator = new QuoteGenerator();

        this.addEventListeners();
    }

    addEventListeners() {
        document.getElementById('generate-quote').addEventListener('click', this.generateQuote);
        document.getElementById('count-words').addEventListener('click', this.analyzeText);
        document.getElementById('reset').addEventListener('click', this.resetQuote);
        document.getElementById('download-pdf').addEventListener('click', this.downloadPDF);
        this.chatBtn.addEventListener('click', this.toggleChat);

        window.addEventListener("offline", () => alert("Network error! Please check your internet connection."));
    }

    generateQuote = async () => {
        const quote = await this.quoteGenerator.fetchQuote();
        this.quoteBox.innerText = quote;
        this.resetCounts();
    };

    analyzeText = () => {
        const text = this.quoteBox.innerText;
        this.wordCount.innerText = text.split(' ').filter(word => word.length > 0).length;
        this.charCount.innerText = text.length;
        this.vowelCount.innerText = (text.match(/[aeiouAEIOU]/g) || []).length;
    };

    resetQuote = () => {
        this.quoteBox.innerText = "Your Quote will appear here...";
        this.resetCounts();
    };

    resetCounts() {
        this.wordCount.innerText = 0;
        this.charCount.innerText = 0;
        this.vowelCount.innerText = 0;
    }

    downloadPDF = () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text(this.quoteBox.innerText, 10, 10);
        doc.save("quote.pdf");
    };

    toggleChat = () => {
        this.chatWindow.style.display = this.chatWindow.style.display === 'none' ? 'block' : 'none';
    };
}

new QuoteApp();