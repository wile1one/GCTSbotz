/*
 This program sends readings from four or more sensor readings and appends
 2 bytes addr data pipes to the beginning of the payloads. The sender will send and
 receive the payload on the same sender/receiver address.

 The receiver is a RPi or UNO accepting 6 pipes and display received payload to the screen

 The receiver will return the receive payload for sender to calculate the rtt
 if the string compared matched to the lcd display

 Max payload size is 32 bytes

Forked RF24 at github :-
https://github.com/stanleyseow/RF24

 Date : 28/03/2013

 Written by Stanley Seow
 stanleyseow@gmail.com
*/

#include <SPI.h>
#include "nRF24L01.h"
#include "RF24.h"
#include "printf.h"


#define M1_Speed  5   //Speed
#define M1_Dir  7   //Direction

#define M2_Speed 6   //Speed
#define M2_Dir 8   //Direction

int Max_Speed = 70; 

//#define RF_SETUP 0x17

// Set up nRF24L01 radio on SPI pin for CE, CSN
RF24 radio(9,10);

// For best performance, use P1-P5 for writing and Pipe0 for reading as per the hub setting
// Below is the settings from the hub/receiver listening to P0 to P5
//const uint64_t pipes[6] = { 0x7365727631LL, 0xF0F0F0F0E1LL, 0xF0F0F0F0E2LL, 0xF0F0F0F0E3LL, 0xF0F0F0F0E4LL, 0xF0F0F0F0E5LL };
const uint64_t pipes[6] = { 0x7365727631LL, 0xF0F0F0F0E1LL, 0xF0F0F0F0E2LL, 0xF0F0F0F0E3LL,  0xF0F0F0F0E4LL, 0xF0F0F0F0E5LL };   //matching the pi


// Example below using pipe5 for writing
//  const uint64_t pipes[2] = { 0xF0F0F0F0E1LL, 0x7365727631LL };


// const uint64_t pipes[2] = { 0xF0F0F0F0E2LL, 0xF0F0F0F0E2LL };
// const uint64_t pipes[2] = { 0xF0F0F0F0E3LL, 0xF0F0F0F0E3LL };
// const uint64_t pipes[2] = { 0xF0F0F0F0E4LL, 0xF0F0F0F0E4LL };
// const uint64_t pipes[2] = { 0xF0F0F0F0E5LL, 0xF0F0F0F0E5LL };
// Pipe0 is F0F0F0F0D2 ( same as reading pipe )

char receivePayload[32];
uint8_t counter=0;

uint8_t RobotNumber = 1;  //gets set in setup, reads on analog pins 0, 1, 2 act as dip switch for robot number selection. analog pins 0, 1, 2 are wired to either GND or 5V.
char isHit[2] = "0";  // is set "1" when the robot gets hit, resets to "0" once radio has sent hit notice or radio.timeout occurs.

String MyCommand = "S";
String OldCommand = "S";


void setup(void)
{
  delay(2000);
  
  pinMode(M1_Speed, OUTPUT);
  pinMode(M1_Dir, OUTPUT);
  pinMode(M2_Speed, OUTPUT);
  pinMode(M2_Dir, OUTPUT); 
 
 pinMode(9,OUTPUT);
 pinMode(10,OUTPUT);
 
 
  Serial.begin(57600);
  
  //Set initial motor control
  digitalWrite(M1_Speed, LOW);
  digitalWrite(M1_Dir, LOW);
  digitalWrite(M2_Speed, LOW);
  digitalWrite(M2_Dir, LOW);
  
  printf_begin();
  printf("Sending nodeID & 4 sensor data\n\r");

  radio.begin();

  // Enable this seems to work better
  radio.enableDynamicPayloads();

  //radio.setAutoAck(1);   //matching the pi
 
  radio.setDataRate(RF24_1MBPS);
  radio.setPALevel(RF24_PA_MAX);
  radio.setChannel(115);
  radio.setRetries(15,15);

  radio.openWritingPipe(pipes[0]); 
  radio.openReadingPipe(1,pipes[1]); 

      
  // Send only, ignore listening mode
  //radio.startListening();

  // Dump the configuration of the rf unit for debugging
  radio.printDetails(); 
  delay(1000); 
}

void loop(void)
{
  uint8_t Data1,Data2,Data3,Data4 = 0;
  char temp[5];
  bool timeout=0;

  // Get the last two Bytes as node-id
  uint16_t nodeID = pipes[0] & 0xff;
  
  char outBuffer[32]=""; // Clear the outBuffer before every loop
  unsigned long send_time, rtt = 0;

    send_time = millis();
    
    // Stop listening and write to radio 
    radio.stopListening();
    sprintf(outBuffer, "%d%s00000000", RobotNumber, isHit);
    
    // Send to hub
    if ( radio.write( outBuffer, strlen(outBuffer)) ) {  // was outBuffer
       printf("Send successful [%s]\n\r",outBuffer); 
    }
    else {
       printf("Send failed\n\r");
    }
    radio.startListening();
    delay(20);  
        
    while ( radio.available() && !timeout ) {
         uint8_t len = radio.getDynamicPayloadSize();
         radio.read( receivePayload, len); 
         
         receivePayload[len] = 0;
         printf("inBuffer:  %s\n\r",receivePayload);
         
         // Display the motor state
           MyCommand = (String)receivePayload[RobotNumber-1];
           if(MyCommand!=OldCommand){
             WriteMotor(MyCommand);
             OldCommand=MyCommand;
           }
             
         // Compare receive payload with outBuffer        
         if ( ! strcmp(outBuffer, receivePayload) ) {
             rtt = millis() - send_time;
             printf("inBuffer --> rtt: %i \n\r",rtt);            
         }       
    
    // Check for timeout and exit the while loop
    if ( millis() - send_time > radio.getMaxTimeout() ) {
         Serial.println("Timeout!!!");
         timeout = 1;
     }          
     delay(10);
   } // End while  
   
   delay(500);
}


void WriteMotor(String MCommand){
  if(MyCommand=="S"){  // STOP
     digitalWrite(M1_Speed, LOW);
     digitalWrite(M1_Dir, LOW);
     digitalWrite(M2_Speed, LOW);
     digitalWrite(M2_Dir, LOW);
   }
   if(MyCommand=="F"){  // Forward
     analogWrite(M1_Speed, Max_Speed);
     digitalWrite(M1_Dir, LOW);
     analogWrite(M2_Speed, Max_Speed);
     digitalWrite(M2_Dir, LOW);
   }
   if(MyCommand=="L"){  // LEFT
     Serial.println("LEFT");
     analogWrite(M1_Speed, Max_Speed);
     digitalWrite(M1_Dir, HIGH);
     analogWrite(M2_Speed, Max_Speed);
     digitalWrite(M2_Dir, LOW);
   }
   if(MyCommand=="R"){  // RIGHT
     Serial.println("RIGHT");
     analogWrite(M1_Speed, Max_Speed);
     digitalWrite(M1_Dir, LOW);
     analogWrite(M2_Speed, Max_Speed);
     digitalWrite(M2_Dir, HIGH);
   }
   if(MyCommand=="B"){  // Backwards
     Serial.println("Backwards");
     analogWrite(M1_Speed, Max_Speed);
     digitalWrite(M1_Dir, HIGH);
     analogWrite(M2_Speed, Max_Speed);
     digitalWrite(M2_Dir, HIGH);
   }
}
