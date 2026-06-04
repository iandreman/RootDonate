const express = require('express');
const stripe = require('stripe')('sk_live_sk_live_51TeUTKAYd3hJ6fUD6lGDVfNyyx2lhxDz0q4BO5nNBdArACR89CtA5pXxHMZIV7zLcoNEvOyD4bFsgmwPsMB3Wk7z00Hf9tmehV'); // ← Your SECRET key
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

app.post('/create-payment', async (req, res) => {
    try {
        const { amount, name, email } = req.body;

        const paymentIntent = await stripe.paymentIntents.create({
            amount: amount,
            currency: 'gbp',
            automatic_payment_methods: { enabled: true },   // Enables Pay by Bank + Cards
            metadata: { name, email }
        });

        res.json({ clientSecret: paymentIntent.client_secret });
    } catch (error) {
        res.json({ error: error.message });
    }
});

app.listen(3000, () => console.log('Server running on port 3000'));