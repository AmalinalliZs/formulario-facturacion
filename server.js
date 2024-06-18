const express = require('express');
const nodemailer = require('nodemailer');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

const app = express();
const port = process.env.PORT || 3000;

const upload = multer({ dest: 'uploads/' });

app.use(express.static('public'));

app.post('/submit-form', upload.fields([
  { name: 'ticket-image', maxCount: 1 },
  { name: 'payment-proof', maxCount: 1 }
]), async (req, res) => {
  const { 'tax-situation': taxSituation, 'fiscal-regime': fiscalRegime, 'payment-method': paymentMethod, 'user-email': userEmail } = req.body;
  const ticketImage = req.files['ticket-image'][0];
  const paymentProof = req.files['payment-proof'] ? req.files['payment-proof'][0] : null;

  const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: 'tu-email@gmail.com',
      pass: 'tu-contraseña'
    }
  });

  const mailOptions = {
    from: 'tu-email@gmail.com',
    to: 'divinasuper9@gmail.com',
    subject: 'Nueva Solicitud de Facturación',
    text: `
      Constancia de situación fiscal: ${taxSituation}
      Régimen Fiscal: ${fiscalRegime}
      Forma de pago: ${paymentMethod}
      Correo Electrónico para enviar factura: ${userEmail}
    `,
    attachments: [
      { filename: ticketImage.originalname, path: ticketImage.path }
    ]
  };

  if (paymentProof) {
    mailOptions.attachments.push({ filename: paymentProof.originalname, path: paymentProof.path });
  }

  try {
    await transporter.sendMail(mailOptions);
    res.status(200).send('Formulario enviado correctamente.');
  } catch (error) {
    console.error(error);
    res.status(500).send('Error al enviar el formulario.');
  }
});

app.listen(port, () => {
  console.log(`Servidor escuchando en http://localhost:${port}`);
});
